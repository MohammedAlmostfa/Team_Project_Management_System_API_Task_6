<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        $id = $request['project_id'];
        $project = Project::find($id);

        // Check if the project exists
        if (!$project) {
            return response()->json([
                'message' => 'Project not found',
                'data' => 'No data available',
            ], 403);
        }

        // Check if the project has a team assigned
        if ($project->status == 'No team has been assigned') {
            return response()->json([
                'message' => 'The project does not have a team yet',
                'data' => 'No data available',
            ], 403);
        } else {
            // Check if the user is an admin or the project manager
            $manager = $project->users()->wherePivot('role', 'Manager')->first();
            if ($manager->id == auth()->user()->id || auth()->user()->role == "admin") {
                return $next($request);
            } else {
                return response()->json([
                    'message' => 'You cannot add a task because you are not the project manager',
                    'data' => 'No data available',
                ], 403);
            }
        }
    }
}
