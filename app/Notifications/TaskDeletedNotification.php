<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $projectName,
        public array $task
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->markdown('emails.Task.task_deleted', [
                'userName' => $notifiable->name,
                'projectName' => $this->projectName,
                'title' => $this->task['title'],
                     'description' => $this->task['description'],
                'due_date' => $this->task['due_date'],
            ]);
    }
}
