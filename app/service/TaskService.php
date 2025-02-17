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
     * Function to show tasks oh project related to user
     * @return array Response array containing message, task data, and status
     */

    public function showalltask()
    {
        try {
            $user = User::find(Auth::user()->id);
            $task = $user->tasksuser;
            if ($task) {
                return [
                    'message' => 'المهام',
                    'data' =>  $task,
                    'status' => 200,
                ];
            } else {
                return [
                    'message' => 'لا يملك الفريق مهام بعد',
                    'data' => [],
                    'status' => 200,
                ];
            }
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء عرض المهام: ' . $e->getMessage());

            return [
                'message' => 'حدث خطأ أثناء عرض المهام: ' ,
                'data' => [],
                'status' => 500,
            ];
        }
    }
    //**________________________________________________________________________________________________

    /**
     * Function to create a new task
     * @param array $data Data of the task
     * @return array Response array containing message, task data, and status
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

            // تحديث last_activity للمدير
            $project = $task->project;
            $project->users()->updateExistingPivot(Auth::user()->id, ['last_activity' => now()]);

            // حساب ساعات المساهمة
            $user = $project->users()->wherePivot('user_id', Auth::user()->id)->first();
            $contribution_hours = $user->updated_at->diffInHours($user->pivot->last_activity);

            // تحديث ساعات المساهمة
            $project->users()->updateExistingPivot($task->user_id, ['contribution_hours' => $contribution_hours]);



            return [
                'message' => 'تم إنشاء المهمة بنجاح',
                'data' => $task,
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء إنشاء المهمة: ' . $e->getMessage());

            return [
                'message' => 'حدث خطأ أثناء إنشاء المهمة: ' . $e->getMessage(),
                'data' => [],
                'status' => 500,
            ];
        }
    }
    //**________________________________________________________________________________________________

    /**
     * Function to update task
     * @param array $data Data of the task
     * @param int $id ID of the task
     * @return array Response array containing message, task data, and status
     */
    public function updateTask($data, $id)
    {
        try {
            // Find the task
            $task = Task::find($id);

            // Check if the task exists
            if (!$task) {
                return [
                    'message' => 'المهمة غير موجودة',
                    'data' => 'لا يوجد بيانات لعرضها',
                    'status' => 403,
                ];
            }
            // Find the project for the task
            $project = $task->project;

            // Check if the project exists
            if (!$project) {
                return [
                    'message' => 'المشروع غير موجود',
                    'data' => 'لا يوجد بيانات لعرضها',
                    'status' => 403,
                ];
            }

            $manager = $project->users()->wherePivot('role', 'Manager')->first();
            $tester = $project->users()->wherePivot('role', 'Tester')->first();
            $authUser = auth()->user();

            // Check if the authenticated user is the manager of the project or an
            if (($manager && $manager->id == $authUser->id)) {
                // Update the task data
                $task->update([
                    'title' => $data['title'] ?? $task->title,
                    'priority' => $data['priority'] ?? $task->priority,
                    'user_id' => $data['user_id'] ?? $task->user_id,
                    'due_date' => $data['due_date'] ?? $task->due_date,
                ]);


                // devlopwe edait his task
            } elseif ($task->user_id == $authUser->id) {
                // Update the task status
                $task->update([
                    'status' => $data['status'] ?? $task->status,
                ]);
                // tester update his task
            } elseif ($tester && $tester->id == $authUser->id) {
                // Update the task description
                $task->update([
                    'description' => $data['description'] ?? $task->description,
                ]);
            } else {
                return [
                    'message' => 'لا يسمح لك بالقيام بهذه العملية',
                    'data' => 'لا يوجد بيانات',
                    'status' => 403,
                ];
            }

            if ((isset($data['status']) && $data['status'] == 'done') || isset($data['description'])) {
                // Update last activity
                $project->users()->updateExistingPivot($task->user_id, ['last_activity' => now()]);
                // Fetch the user with pivot data
                $user = $project->users()->wherePivot('user_id', $task->user_id)->first();
                // Calculate contribution hours
                $contribution_hours = $user->updated_at->diffInHours($user->pivot->last_activity);
                // Update contribution hours
                $project->users()->updateExistingPivot($task->user_id, ['contribution_hours' => $contribution_hours]);
            }

            return [
                'message' => 'تم تحديث المهمة بنجاح',
                'data' => $task,
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء تحديث المهمة: ' . $e->getMessage());

            return [
                'message' => 'حدث خطأ أثناء تحديث المهمة: '. $e->getMessage(),
                'data' => [],
                'status' => 500,
            ];
        }
    }

    //**________________________________________________________________________________________________


    /**
     * Function to delete task
     * @param int $id ID of the task
     * @return array Response array containing message, task, and status
     */
    public function deleteTask($id)
    {
        try {
            // Find task by ID
            $task = Task::find($id);

            // Check if task exists
            if (!$task) {
                return [
                    'message' => 'المهمة غير موجودة',
                    'data' => 'لا يوجد بيانات لعرضها',
                    'status' => 403,
                ];
            }
            // Find project of task
            $project = $task->project;
            $manager = $project->users()->wherePivot('role', 'Manager')->first();
            // Check if the authenticated user is the manager of the task or an
            if (($manager && $manager->id == Auth::user()->id)) {
                // Delete task
                $task->delete();
                return [
                    'message' => 'تم حذف المهمة بنجاح',
                    'status' => 200,
                ];
            } else {
                return [
                    'message' => 'لا يحق لك حذف هذه المهمة',
                    'status' => 403,
                ];
            }
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء حذف المهمة: ' . $e->getMessage());

            return [
                'message' => 'حدث خطأ أثناء حذف المهمة: ',
                'status' => 500,
            ];
        }
    }


    //**________________________________________________________________________________________________

    /**
     * *Function to show task
     * *@param int $id ID of the task
     * *@return array Response array containing message, task data, and status
     */
    public function showTask($id)
    {
        try {
            // Find task by ID
            $task = Task::find($id);
            // Check if task exists
            if (!$task) {
                return [
                    'message' => 'المهمة غير موجودة',
                    'data' => 'لا يوجد بيانات لعرضها',
                    'status' => 403,
                ];
            }
            // Find project of task
            $project = $task->project;
            $manager = $project->users()->wherePivot('role', 'Manager')->first();
            $tester = $project->users()->wherePivot('role', 'Tester')->first();

            // Check if the authenticated user is the manager, tester, or admin or task owner
            if ($task->user_id == Auth::user()->id  || $manager->id == Auth::user()->id || $tester->id == Auth::user()->id) {
                return [
                    'message' => 'بيانات المهمة',
                    'data' => $task,
                    'status' => 200,
                ];
            } else {

                return [
                    'message' => 'لا يحق لك عرض هذه المهمة',
                    'data' => 'لا يوجد بيانات لعرضها',
                    'status' => 403,
                ];
            }
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء عرض المهمة: ' . $e->getMessage());

            return [
                'message' => 'حدث خطأ أثناء عرض المهمة: ' ,
                'data' => [],
                'status' => 500,
            ];
        }
    }
    //**________________________________________________________________________________________________

    /**
        * *Function to  devloper show his  task
        * *@param array $data(data to filter data)
        * *@return array Response array containing message, tasks data, and status
        */

    public function show_his_tasks($data)
    {
        try {
            $user = User::find(Auth::user()->id);
            $tasks = $user->tasks();

            if (!$tasks) {
                return [
                    'message' => 'ليس لديك مهام',
                    'data' => [],
                    'status' => 200,
                ];
            } else {
                // filter by name an return it ordaring
                if (!empty($data['title'])) {
                    $tasks =  $tasks->prioritytask($data['title']);
                }
                // filter by priorty
                elseif (!empty($data['priority'])) {
                    $tasks =  $tasks->priority($data['priority']);
                }
                // filter by status
                if (!empty($data['status'])) {
                    $tasks =  $tasks->status($data['status']);
                }

                return [
                    'message' => 'عرض المهام بنجاح',
                    'data' => $tasks->get(),
                    'status' => 200,
                ];

            }
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء عرض المهام: ' . $e->getMessage());

            return [
                'message' => 'حدث خطأ أثناء عرض المهام',
                'data' => [],
                'status' => 500,
            ];
        }
    }

}
