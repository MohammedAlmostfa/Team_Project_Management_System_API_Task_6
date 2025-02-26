<?php
namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Auth\Access\AuthorizationException;

class TaskPolicy
{
    /**
     * Check if the user can create a task.
     */
    public function create(User $user, Project $project)
    {
        $manager = $project->users()->wherePivot('role', 'Manager')->first();
        if (!($manager && $manager->id == $user->id || $user->role == "admin")) {
            throw new AuthorizationException('You are not authorized to create a task in this project.');
        }
        if ($project->status == 0) {
            throw new AuthorizationException('The project is not active.');
        }

        return true;
    }

    /**
     * Check if the user can update a task as a manager.
     */
    public function updateAsManger(User $user, Project $project)
    {
        $manager = $project->users()->wherePivot('role', 'Manager')->first();
        if (!($manager && $manager->id == $user->id || $user->role == "admin")) {
            throw new AuthorizationException('You are not authorized to update this task as a manager.');
        }
        if ($project->status == 0) {
            throw new AuthorizationException('The project is not active.');
        }

        return true;
    }

    /**
     * Check if the user can update a task as a regular user.
     */
    public function updateAsUser(User $user, Task $task)
    {
        if ($task->user_id !== $user->id) {
            throw new AuthorizationException('You are not authorized to update this task.');
        }

        return true;
    }

    /**
     * Check if the user can update a task as a tester.
     */
    public function updateAsTaster(User $user, Project $project)
    {
        $taster = $project->users()->wherePivot('role', 'Taster')->first();
        if (!($taster && $taster->id == $user->id || $user->role == "admin")) {
            throw new AuthorizationException('You are not authorized to update this task as a tester.');
        }

        return true;
    }

    /**
     * Check if the user can delete a task.
     */
    public function deleteTask(User $user, Project $project)
    {
        $manager = $project->users()->wherePivot('role', 'Manager')->first();
        if (!($manager && $manager->id == $user->id || $user->role == "admin")) {
            throw new AuthorizationException('You are not authorized to delete this task.');
        }

        return true;
    }

    /**
     * Check if the user can view a task.
     */
    public function showTask(User $user, Task $task)
    {
        $project = $task->project;
        $manager = $project->users()->wherePivot('role', 'Manager')->first();
        $tester = $project->users()->wherePivot('role', 'Tester')->first();

        if ($task->user_id !== $user->id && (!$manager || $manager->id !== $user->id) && (!$tester || $tester->id !== $user->id)) {
            throw new AuthorizationException('You are not authorized to view this task.');
        }

        return true;
    }
}
