<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamDeletNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $projectName,
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


        return (new MailMessage)
            ->markdown('emails.team_deleted', [
                'userName' => $userName,
                'projectName' => $projectName,

            ]);
    }

}
