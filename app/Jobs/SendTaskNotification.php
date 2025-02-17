<?php
namespace App\Jobs;

use App\Models\User;
use App\Notifications\NewTaskNotification;
use App\Notifications\TaskUpdatedNotification;
use App\Notifications\TaskDeletedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $projectName;
    protected $task;
    protected $type;

    public function __construct(User $user, string $projectName, array $task, string $type = 'created')
    {
        $this->user = $user;
        $this->projectName = $projectName;
        $this->task = $task;
        $this->type = $type;
    }

    public function handle()
    {
        // تحديد نوع الإيميل بناءً على الحدث
        if ($this->type === 'created') {
            $this->user->notify(new NewTaskNotification($this->projectName, $this->task));
        } elseif ($this->type === 'updated') {
            $this->user->notify(new TaskUpdatedNotification($this->projectName, $this->task));

        } elseif ($this->type === 'deleted') {
            $this->user->notify(new TaskDeletedNotification($this->projectName, $this->task));
        }
    }
}
