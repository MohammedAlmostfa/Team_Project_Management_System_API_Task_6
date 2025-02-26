<?php

namespace App\Service;

use Exception;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewTaskNotification;

class TaskService
{
    /**
     * Display all tasks of a project related to the authenticated user.
     *
     * @return array Response array containing message, task data, and status.
     */
    public function showAllTasks()
    {
        try {
            $user = User::find(Auth::user()->id);
            $tasks = $user->tasksuser->paginate(10);

            if ($tasks->isNotEmpty()) {
                return [
                    'message' => 'Tasks retrieved successfully',
                    'data' => $tasks,
                    'status' => 200,
                ];
            } else {
                return [
                    'message' => 'The team does not have any tasks yet',
                    'data' => [],
                    'status' => 200,
                ];
            }
        } catch (Exception $e) {
            Log::error('An error occurred while retrieving tasks: ' . $e->getMessage());

            return [
                'message' => 'An error occurred while retrieving tasks',
                'data' => [],
                'status' => 500,
            ];
        }
    }

    /**
     * Create a new task.
     *
     * @param array $data Task data.
     * @return array Response array containing message, task data, and status.
     */
    public function createTask($data)
    {
        try {
            $task = Task::create([
                'title' => $data['title'],
                'priority' => $data['priority'],
                'user_id' => $data['user_id'],
                'description' => $data['description'],
                'project_id' => $data['project_id'],
                'due_date' => $data['due_date'],
            ]);

            // Update last_activity for the manager
            $project = $task->project;
            $project->users()->updateExistingPivot(Auth::user()->id, ['last_activity' => now()]);

            // Calculate contribution hours
            $user = $project->users()->wherePivot('user_id', Auth::user()->id)->first();
            $contribution_hours = $user->updated_at->diffInHours($user->pivot->last_activity);

            // Update contribution hours
            $project->users()->updateExistingPivot($task->user_id, ['contribution_hours' => $contribution_hours]);

            return [
                'message' => 'Task created successfully',
                'data' => $task,
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('An error occurred while creating the task: ' . $e->getMessage());

            return [
                 'message' => 'An error occurred while updating the task: ' ,
                 'data' => [],
                 'status' => 500,
             ];
        }
    }


    /**
        * Update a task as a Devloper.
        *
        * @param array $data Updated task data.
        * @param int $id ID of the task.
        * @return array Response array containing message, task data, and status.
        */


    public function UserUpdateTask($data, $id)
    {
        try {
            $task = Task::find($id);

            if (!$task) {
                return [
                    'message' => 'Task not found',
                    'data' => 'No data to display',
                    'status' => 403,
                ];
            }

            $project = $task->project;

            if (!$project) {
                return [
                    'message' => 'Project not found',
                    'data' => 'No data to display',
                    'status' => 403,
                ];
            }
            $authUser = Auth::user();

            if ($task->user_id == $authUser->id) {
                $task->update([
                    'status' => $data['status'] ?? $task->status,
                ]);
            }
            if ((isset($data['status']) && $data['status'] == 'done') || isset($data['description'])) {
                $project->users()->updateExistingPivot($task->user_id, ['last_activity' => now()]);
                $user = $project->users()->wherePivot('user_id', $task->user_id)->first();
                $contribution_hours = $user->updated_at->diffInHours($user->pivot->last_activity);
                $project->users()->updateExistingPivot($task->user_id, ['contribution_hours' => $contribution_hours]);
            }

        } catch (Exception $e) {
            Log::error('An error occurred while updating the task: ' . $e->getMessage());

            return [
                'message' => 'An error occurred while updating the task: ' ,
                'data' => [],
                'status' => 500,
            ];
        }
    }
    /**
    * Update a task as a Taster.
    *
    * @param array $data Updated task data.
    * @param int $id ID of the task.
    * @return array Response array containing message, task data, and status.
    */

    public function TasterUpdateTask($data, $id)
    {
        try {
            $task = Task::find($id);

            if (!$task) {
                return [
                    'message' => 'Task not found',
                    'data' => 'No data to display',
                    'status' => 403,
                ];
            }

            $project = $task->project;

            if (!$project) {
                return [
                    'message' => 'Project not found',
                    'data' => 'No data to display',
                    'status' => 403,
                ];
            }
            $authUser = Auth::user();
            $tester = $project->users()->wherePivot('role', 'Tester')->first();

            if ($tester && $tester->id == $authUser->id) {
                $task->update([
                    'description' => $data['description'] ?? $task->description,
                ]);
            }
        } catch (Exception $e) {
            Log::error('An error occurred while updating the task: ' . $e->getMessage());

            return [
                'message' => 'An error occurred while updating the task: ' . $e->getMessage(),
                'data' => [],
                'status' => 500,
            ];
        }
    }

    /**
        * Update a task as a Manger.
        *
        * @param array $data Updated task data.
        * @param int $id ID of the task.
        * @return array Response array containing message, task data, and status.
        */

    public function ManagerUpdateTask($data, $id)
    {
        try {
            $task = Task::find($id);

            if (!$task) {
                return [
                    'message' => 'Task not found',
                    'data' => 'No data to display',
                    'status' => 403,
                ];
            }

            $project = $task->project;

            if (!$project) {
                return [
                    'message' => 'Project not found',
                    'data' => 'No data to display',
                    'status' => 403,
                ];
            }
            $authUser = Auth::user();
            $manager = $project->users()->wherePivot('role', 'Manager')->first();


            if (($manager && $manager->id == $authUser->id)) {
                $task->update([
                    'title' => $data['title'] ?? $task->title,
                    'priority' => $data['priority'] ?? $task->priority,
                    'user_id' => $data['user_id'] ?? $task->user_id,
                    'due_date' => $data['due_date'] ?? $task->due_date,
                ]);
            }
        } catch (Exception $e) {
            Log::error('An error occurred while updating the task: ' . $e->getMessage());

            return [
                'message' => 'An error occurred while updating the task: ' . $e->getMessage(),
                'data' => [],
                'status' => 500,
            ];
        }
    }



    /**
     * Delete a task.
     *
     * @param int $id ID of the task.
     * @return array Response array containing message and status.
     */
    public function deleteTask($id)
    {
        try {
            $task = Task::find($id);

            if (!$task) {
                return [
                    'message' => 'Task not found',
                    'data' => 'No data to display',
                    'status' => 403,
                ];
            }

            $project = $task->project;
            $manager = $project->users()->wherePivot('role', 'Manager')->first();


        } catch (Exception $e) {
            Log::error('An error occurred while deleting the task: ' . $e->getMessage());

            return [
                'message' => 'An error occurred while deleting the task',
                'status' => 500,
            ];
        }
    }

    /**
     * Display a specific task.
     *
     * @param int $id ID of the task.
     * @return array Response array containing message, task data, and status.
     */
    public function showTask($id)
    {
        try {
            $task = Task::find($id);

            if (!$task) {
                return [
                    'message' => 'Task not found',
                    'data' => 'No data to display',
                    'status' => 403,
                ];
            }


        } catch (Exception $e) {
            Log::error('An error occurred while retrieving the task: ' . $e->getMessage());

            return [
                'message' => 'An error occurred while retrieving the task',
                'data' => [],
                'status' => 500,
            ];
        }
    }

    /**
     * Display tasks assigned to the authenticated user.
     *
     * @param array $data Filters for tasks (e.g., title, priority, status).
     * @return array Response array containing message, tasks data, and status.
     */
    public function showHisTasks($data)
    {
        try {
            $user = User::find(Auth::user()->id);
            $tasks = $user->tasks();

            if (!$tasks) {
                return [
                    'message' => 'You do not have any tasks',
                    'data' => [],
                    'status' => 200,
                ];
            } else {
                if (!empty($data['title'])) {
                    $tasks = $tasks->prioritytask($data['title']);
                } elseif (!empty($data['priority'])) {
                    $tasks = $tasks->priority($data['priority']);
                }

                if (!empty($data['status'])) {
                    $tasks = $tasks->status($data['status']);
                }

                return [
                    'message' => 'Tasks retrieved successfully',
                    'data' => $tasks->get(),
                    'status' => 200,
                ];
            }
        } catch (Exception $e) {
            Log::error('An error occurred while retrieving tasks: ' . $e->getMessage());

            return [
                'message' => 'An error occurred while retrieving tasks',
                'data' => [],
                'status' => 500,
            ];
        }
    }
}
