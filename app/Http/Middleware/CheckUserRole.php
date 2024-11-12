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
        // check if project exists
        if (!$project) {
            return response()->json([
                'message' => 'المشروع غير موجود',
                'data' => 'لا يوجد بيانات لعرضها',

            ], 403);
        }
        if ($project->status == 'No team has been assigned') {
            return response()->json([
                'message' => 'المشروع ليس له فريق بعد ',
                'data' => 'لا يوجد بيانات لعرضها',

            ], 403);
        } else {
            // check if user is afmin or manger
            $manager = $project->users()->wherePivot('role', 'Manager')->first();
            if ($manager->id == auth()->user()->id || auth()->user()->role == "admin") {
                return $next($request);
            } else {

                return response()->json([
                    'message' => ' لا يمكنك اضافة مهمة فانت لست مدير المشروع ',
                    'data' => 'لا يوجد بيانات ',
                ], 403);
            }
        }
    }
}
