<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;
use Illuminate\Auth\Access\AuthorizationException;

class TeamPolicy
{
    public function createTeam(User $user, Project $project)
    {
        return $project->status == 'No team has been assigned';

    }

    public function updateTeam(User $user, Project $project)
    {


        return $project->status == 'A team has been appointed';

    }

    public function deleteTeam(User $user, Project $project)
    {


        return $project->status == 'A team has been appointed';

    }
}
