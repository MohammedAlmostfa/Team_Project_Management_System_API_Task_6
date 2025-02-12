<?php
// app/Notifications/TeamNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTeamNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $projectName,
        public string $role
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
        $role = $this->role;
        $projectUrl = url('/projects');

        return (new MailMessage)
            ->markdown('emails.project_added', [
                'userName' => $userName,
                'projectName' => $projectName,
                'role' => $role,
                'projectUrl' => $projectUrl,
            ]);
    }

}
