<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTaskNotification extends Notification implements ShouldQueue
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
        $userName = $notifiable->name;
        $projectName = $this->projectName;
        $task = $this->task;

        return (new MailMessage)
            ->markdown('emails.Task.new_task_add', [
                'userName' => $userName,
                'projectName' => $projectName,
                'title' => $task['title'],
                'description' => $task['description'],
                'due_date' => $task['due_date'],
            ]);
    }
}
