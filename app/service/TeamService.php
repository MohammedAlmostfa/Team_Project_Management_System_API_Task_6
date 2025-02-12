<?php


namespace App\service;

use Exception;
use App\Models\User;
use App\Models\Project;
use App\Policies\ProjectPolicy;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Notifications\NewTeamNotification;
use App\Notifications\TeamDeletNotification;

class TeamService
{
    public function createTeam(array $data, string $id): array
    {
        try {
            $project = Project::find($id);

            if (!$project) {
                return [
                    'message' => 'المشروع غير موجود',
                    'status' => 404,
                ];
            }

            if (Gate::denies('canAddTeam', $project)) {
                return [
                    'message' => 'لا يمكنك إضافة فريق آخر فقد تم تعيين فريق',
                    'status' => 200,
                ];
            }

            // إضافة المطورين
            $developers = User::findMany($data['developers_ids']);
            $project->users()->attach($data['developers_ids'], ['role' => 'Developer']);
            foreach ($developers as $developer) {
                $developer->notify(new NewTeamNotification($project->name, 'Developer'));
            }

            // إضافة المدير
            $manager = User::find($data['manager']);
            $project->users()->attach($data['manager'], ['role' => 'Manager']);
            $manager->notify(new NewTeamNotification($project->name, 'Manager'));

            // إضافة الفاحص
            $tester = User::find($data['tester']);
            $project->users()->attach($data['tester'], ['role' => 'Tester']);
            $tester->notify(new NewTeamNotification($project->name, 'Tester'));

            // تحديث حالة المشروع
            $project->status = 1;
            $project->save();

            return [
                'message' => 'تم إنشاء الفريق',
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء إنشاء الفريق: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء إنشاء الفريق',
                'status' => 500,
            ];
        }
    }

    public function updateTeam(array $data, string $id): array
    {
        try {
            $project = Project::find($id);

            if (!$project) {
                return [
                    'message' => 'المشروع غير موجود',
                    'status' => 404,
                ];
            }

            // التحقق باستخدام الـ Policy
            if (Gate::denies('canUpdateTeam', $project)) {
                return [
                    'message' => 'لم يتم تعيين فريق بعد',
                    'status' => 404,
                ];
            }


            $oldUser = $project->users()->find($data['old_user_id']);
            $oldUserRole = $oldUser->pivot->role;
            $project->users()->detach($data['old_user_id']);
            $oldUser->notify(new TeamDeletNotification($project->name));


            $project->users()->attach($data['new_user_id'], ['role' => $oldUserRole]);
            $useradd=User::find($data['new_user_id']);
            $useradd->notify(new NewTeamNotification($project->name, $oldUserRole));








            return [
                'message' => 'تمت عملية التحديث بنجاح',
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء تحديث الفريق: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء تحديث الفريق: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }
    public function deleteTeam(string $id): array
    {
        try {
            $project = Project::find($id);

            if (!$project) {
                return [
                    'message' => 'المشروع غير موجود',
                    'status' => 404,
                ];
            }

            // التحقق باستخدام الـ Policy
            if (Gate::denies('canDeleteTeam', $project)) {
                return [
                    'message' => 'لم يتم تعيين فريق بعد',
                    'status' => 404,
                ];
            }

            // الحصول على المستخدمين في الفريق
            $users = $project->users;

            // حذف الفريق وإرسال الإشعارات للمستخدمين
            foreach ($users as $user) {
                $project->users()->detach($user->id);
                $user->notify(new TeamDeletNotification($project->name));
            }

            // تحديث حالة المشروع
            $project->status = 0;
            $project->save();

            return [
                'message' => 'تم إلغاء الفريق',
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء إلغاء الفريق: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء إلغاء الفريق',
                'status' => 500,
            ];
        }
    }

}
