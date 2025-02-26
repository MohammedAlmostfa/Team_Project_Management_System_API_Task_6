<?php

namespace App\Service;

use Exception;
use App\Models\User;
use App\Models\Project;
use App\Jobs\SendTeamNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class TeamService
{
    /**
     * Create a new team for a project.
     *
     * @param array $data Team data including developers, manager, and tester IDs.
     * @param string $id Project ID.
     * @return array Response containing status, message, and data.
     */
    public function createTeam(array $data, string $id): array
    {
        DB::beginTransaction();

        try {
            // Find the project
            $project = Project::findOrFail($id);

            // Attach developers
            $developers = User::findMany($data['developers_ids']);
            $project->users()->attach($data['developers_ids'], ['role' => 'Developer']);

            // Attach manager
            $manager = User::findOrFail($data['manager']);
            $project->users()->attach($data['manager'], ['role' => 'Manager']);

            // Attach tester
            $tester = User::findOrFail($data['tester']);
            $project->users()->attach($data['tester'], ['role' => 'Tester']);

            // Dispatch notifications to all team members
            foreach ($project->users as $user) {
                SendTeamNotification::dispatch($user->id, $project->name, $user->pivot->role, "create");
            }

            // Update project status
            $project->status = 1; // Assuming 1 means "Team Assigned"
            $project->save();

            DB::commit();

            return [
                'message' => 'Team created successfully.',
                'data' => $data,
                'status' => 200,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error while creating team: ' . $e->getMessage());
            return [
                'message' => 'Error while creating team.',
                'data' => null,
                'status' => 500,
            ];
        }
    }

    /**
     * Update a team member in a project.
     *
     * @param array $data Data containing old and new user IDs.
     * @param string $id Project ID.
     * @return array Response containing status and message.
     */
    public function updateTeam(array $data, string $id): array
    {
        DB::beginTransaction();

        try {
            // Find the project
            $project = Project::findOrFail($id);
            if (!$project) {
                return [
                    'message' => 'project not found',
                    'data' => null,
                    'status' => 200,
                ];
            }
            // Find the old user and their role
            $oldUser = $project->users()->findOrFail($data['old_user_id']);
            $oldUserRole = $oldUser->pivot->role;

            // Detach the old user and send a delete notification
            $project->users()->detach($data['old_user_id']);
            SendTeamNotification::dispatch($data['old_user_id'], $project->name, $oldUserRole, "delete");

            // Attach the new user and send a create notification
            $project->users()->attach($data['new_user_id'], ['role' => $oldUserRole]);
            SendTeamNotification::dispatch($data['new_user_id'], $project->name, $oldUserRole, "create");

            DB::commit();

            return [
                'message' => 'Team updated successfully.',
                'data' => $data,
                'status' => 200,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error while updating team: ' . $e->getMessage());
            return [
                'message' => 'Error while updating team',
                'status' => 500,
            ];
        }
    }

    /**
     * Delete a team from a project.
     *
     * @param string $id Project ID.
     * @return array Response containing status and message.
     */
    public function deleteTeam(string $id): array
    {
        DB::beginTransaction();

        try {
            // Find the project
            $project = Project::findOrFail($id);

            // Check if the team can be deleted


            // Get all users and their roles
            $users = $project->users;

            // Detach users and send delete notifications
            foreach ($users as $user) {
                $role = $user->pivot->role;
                $project->users()->detach($user->id);
                SendTeamNotification::dispatch($user->id, $project->name, $role, "delete");
            }

            // Update project status
            $project->status = 0; // Assuming 0 means "No Team Assigned"
            $project->save();

            DB::commit();

            return [
                'message' => 'Team deleted successfully.',
                'status' => 200,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error while deleting team: ' . $e->getMessage());
            return [
                'message' => 'Error while deleting team.',
                'status' => 500,
            ];
        }
    }
}
