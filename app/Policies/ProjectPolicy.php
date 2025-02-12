<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * التحقق مما إذا كان يمكن إضافة فريق إلى المشروع.
     */
    public function canAddTeam(User $user, Project $project): bool
    {
        return $project->status !== "A team has been appointed";
    }

    /**
     * التحقق مما إذا كان يمكن تحديث الفريق.
     */
    public function canUpdateTeam(User $user, Project $project): bool
    {
        return $project->status !== "No team has been assigned";
    }

    /**
     * التحقق مما إذا كان يمكن حذف الفريق.
     */
    public function canDeleteTeam(User $user, Project $project): bool
    {
        return $project->status !== "No team has been assigned";
    }
}
