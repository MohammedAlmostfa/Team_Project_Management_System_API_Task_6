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
    public function createTeam(array $data, string $id): array
    {
        DB::beginTransaction();

        try {
            $project = Project::find($id);

            if (!$project) {
                return [
                    'message' => 'المشروع غير موجود',
                    'status' => 404,
                ];
            }

            if (Gate::denies('canAddTeam', $project)) {
                return [
                    'message' => 'لا يمكنك إضافة فريق آخر فقد تم تعيين فريق',
                    'status' => 200,
                ];
            }

            // Attach developers
            $developers = User::findMany($data['developers_ids']);
            $project->users()->attach($data['developers_ids'], ['role' => 'Developer']);

            // Attach manager
            $manager = User::find($data['manager']);
            $project->users()->attach($data['manager'], ['role' => 'Manager']);

            // Attach tester
            $tester = User::find($data['tester']);
            $project->users()->attach($data['tester'], ['role' => 'Tester']);

            // Dispatch notifications
            foreach ($project->users as $user) {
                SendTeamNotification::dispatch($user->id, $project->name, $user->pivot->role, "create");
            }

            // Update project status
            $project->status = 1;
            $project->save();

            DB::commit();

            return [
                'message' => 'تم إنشاء الفريق',
                'status' => 200,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('حدث خطأ أثناء إنشاء الفريق: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء إنشاء الفريق',
                'status' => 500,
            ];
        }
    }

    public function updateTeam(array $data, string $id): array
    {
        DB::beginTransaction();

        try {
            $project = Project::find($id);

            if (!$project) {
                return [
                    'message' => 'المشروع غير موجود',
                    'status' => 404,
                ];
            }

            if (Gate::denies('canUpdateTeam', $project)) {
                return [
                    'message' => 'لم يتم تعيين فريق بعد',
                    'status' => 404,
                ];
            }

            // Find the old user and their role
            $oldUser = $project->users()->find($data['old_user_id']);
            if (!$oldUser) {
                return [
                    'message' => 'المستخدم القديم غير موجود',
                    'status' => 404,
                ];
            }

            $oldUserRole = $oldUser->pivot->role;

            // Detach the old user and send delete notification
            $project->users()->detach($data['old_user_id']);
            SendTeamNotification::dispatch($data['old_user_id'], $project->name, $oldUserRole, "delete");

            // Attach the new user and send create notification
            $project->users()->attach($data['new_user_id'], ['role' => $oldUserRole]);
            SendTeamNotification::dispatch($data['new_user_id'], $project->name, $oldUserRole, "create");

            DB::commit();

            return [
                'message' => 'تمت عملية التحديث بنجاح',
                'status' => 200,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('حدث خطأ أثناء تحديث الفريق: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء تحديث الفريق: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function deleteTeam(string $id): array
    {
        DB::beginTransaction();

        try {
            $project = Project::find($id);

            if (!$project) {
                return [
                    'message' => 'المشروع غير موجود',
                    'status' => 404,
                ];
            }

            if (Gate::denies('canDeleteTeam', $project)) {
                return [
                    'message' => 'لم يتم تعيين فريق بعد',
                    'status' => 404,
                ];
            }

            // Get users and their roles
            $users = $project->users;

            // Detach users and send delete notifications
            foreach ($users as $user) {
                $role = $user->pivot->role;
                $project->users()->detach($user->id);
                SendTeamNotification::dispatch($user->id, $project->name, $role, "delete");
            }

            // Update project status
            $project->status = 0;
            $project->save();

            DB::commit();

            return [
                'message' => 'تم إلغاء الفريق',
                'status' => 200,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('حدث خطأ أثناء إلغاء الفريق: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء إلغاء الفريق',
                'status' => 500,
            ];
        }
    }
}
