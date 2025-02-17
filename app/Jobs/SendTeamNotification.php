<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\Team\NewTeamNotification;
use App\Notifications\Team\TeamDeletNotification;

class SendTeamNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $projectName;
    protected $role;
    protected $type;

    public function __construct(int $userId, string $projectName, string $role, string $type)
    {
        $this->userId = $userId;
        $this->projectName = $projectName;
        $this->role = $role;
        $this->type = $type;
    }

    public function handle()
    {
        $user = User::find($this->userId);


        if ($this->type == "create") {
            $user->notify(new NewTeamNotification($this->projectName, $this->role));
        } elseif ($this->type == "delete") {
            $user->notify(new TeamDeletNotification($this->projectName));
        }
    }
}
