<?php

namespace App\Service;

use App\Models\Project;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TeamService
{


    /**
     * function  created to add team members
     * @param $data
     * @param $id
     * @return array(message,project data,status)
     */
    public function createTeam(array $data, string $id)
    {
        try {
            //find project
            $project = Project::find($id);
            // add team
            if ($project) {
                //check if project has team
                if ($project->status == "A team has been appointed") {
                    return [
                        'message' => 'لا يمكنك اضافة فريق اخر فقد تم تم تعين فريق  ',
                        'status' => 200,
                    ];
                } else {
                    //add developers
                    $project->users()->attach($data['developers_ids'], ['role' => 'Developer']);
                    //add manger
                    $project->users()->attach($data['manager'], ['role' => 'Manager']);
                    //add tester
                    $project->users()->attach($data['tester'], ['role' => 'Tester']);
                    // update status of project
                    $project->status = 1;
                    $project->save();
                    //return data
                    return [
                        'message' => 'تم إنشاء الفريق   ',
                        'status' => 200,
                    ];
                }
            } else {
                //return data
                return [
                    'message' => 'المشروع غير موجود',
                    'status' => 404,
                ];
            }
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء إنشاء الفريق: ' . $e->getMessage());
            //return data
            return [
                'message' => 'حدث خطأ أثناء إنشاء الفريق',
                'status' => 500,
            ];
        }
    }
    //**________________________________________________________________________________________________
    /**
     * function  created to updata team members
     * @param $data(old uer id,new uerid ,role)
     * @param $id(id of project)
     * @return array(message,project data,status)
     */
    public function updateTeam($data, $id)
    {
        try {
            // Find project
            $project = Project::find($id);
            // Check if project exists
            if (!$project) {
                return [
                    'message' => 'المشروع غير موجود',
                    'status' => 404,
                ];
            }
            // Check if team has been assigned
            if ($project->status == "'No team has been assigned'") {
                return [
                    'message' => 'لم يتم تعيين فريق بعد',
                    'status' => 404,
                ];
            }
            $project->users()->detach($data['old_user_id']);


            // Update users in the project
            $project->users()->sync([
                $data['new_user_id'] => [
                    'contribution_hours' => null,
                    'last_activity' => null,
                    'role' => $data['role'],
                ]
            ], false); // false يعني عدم إزالة المستخدمين الآخرين

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
    //**________________________________________________________________________________________________
    /**
     * function  created to delet team members
     * @param $id(id of project)
     * @return array(message,project ,status)
     */
    public function deleteTeam($id)
    {
        try {
            // finf project
            $project = Project::find($id);
            // check if  project exists
            if ($project) {

                // check if project has been add team
                if ($project->status == "No team has been assigned") {
                    return [
                        'message' => 'لم يتم تعيين فريق بعد',
                        'data' => [],
                        'status' => 404,
                    ];
                } else {
                    // delet team
                    $project->users()->detach();
                    //update project status
                    $project->status = 0;
                    $project->save();
                    return [
                        'message' => 'تم إلغاء الفريق',
                        'status' => 200,
                    ];
                }
            } else {
                return [
                    'message' => 'المشروع غير موجود',
                    'status' => 404,
                ];
            }
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء إلغاء الفريق: ' . $e->getMessage());
            // إرجاع البيانات
            return [
                'message' => 'حدث خطأ أثناء إلغاء الفريق',
                'status' => 500,
            ];
        }
    }
}
