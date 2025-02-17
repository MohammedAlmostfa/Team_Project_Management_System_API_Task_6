<?php
namespace App\Observers;

use App\Models\Task;
use App\Jobs\SendTaskNotification;
use App\Notifications\TaskUpdatedNotification;
use App\Notifications\TaskDeletedNotification;

class TaskObserver
{

    public function created(Task $task)
    {

        $user = $task->user;
        $projectName = $task->project->name;
        SendTaskNotification::dispatch($user, $projectName, $task->toArray());
    }


    public function updated(Task $task)
    {
        $user = $task->user;
        $projectName = $task->project->name;
        SendTaskNotification::dispatch($user, $projectName, $task->toArray(), 'updated');
    }


    public function deleted(Task $task)
    {

        $user = $task->user;
        $projectName = $task->project->name;

        SendTaskNotification::dispatch($user, $projectName, $task->toArray(), 'deleted');
    }
}
