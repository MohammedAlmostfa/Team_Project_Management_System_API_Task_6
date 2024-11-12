<?php

namespace App\Service;

use App\Models\Project;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectService
{

    /**
     * function  created to show all  projects
     * @param nothing
     * @return array(message,project data,status)
     */
    public function showallProjects($data)
    {
        try {
            if($data['status']) {
                //get all projects witg lastest and oldest tasks
                $projects = Project::with('lastoftask', 'oldoftask')->status($data['status'])->get();
            } else {

                $projects = Project::with('lastoftask', 'oldoftask')->get();

            }
            //return array(message,project data ,status)
            return [
                'message' => 'تمت عملية العرض بنجاح',
                'data' => $projects,
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء العرض المشاريع: ' . $e->getMessage());
            //return array(message,project data ,status)
            return [
                'message' => 'حدث خطأ أثناء العرض المشاريع',
                'data' => 'لا يوحد ببيانات',
                'status' => 500,
            ];
        }
    }
    //**________________________________________________________________________________________________
    /**
     * function  created to creat new project
     * @param data(data to inserted)
     * @return array(message, project data,status)
     */
    public function createProject($data)
    {
        try {
            // create project
            $project = Project::create($data);
            //return array(message, project data, status
            return [
                'message' => 'تم إنشاء المشروع بنجاح',
                'data' => $project,
                'status' => 200,
            ];
            //exception
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء إنشاء المشروع: ');
            //return array(message, project data, status
            return [
                'message' => 'حدث خطأ أثناء إنشاء المشروع',
                'data' => 'لا يوجد بيانات',
                'status' => 500,
            ];
        }
    }
    //**________________________________________________________________________________________________
    /**
     * function  created to update  project
     * @param data(data to inserted)
     * @param $id(id of project)
     * @return array(message,status)
     */
    public function updateProject($data, $id)
    {
        try {
            //update project
            $project = Project::find($id);
            //check project is exict
            if ($project) {
                //return array(message, status)
                $project->update([
                    'name' => $data['name'] ?? $project->name,
                    'description' => $data['description'] ?? $project->description,
                ]);
                //return array(message, status)
                return [
                    'message' => 'تمت عملية التحديث بنجاح',
                    'status' => 200,
                ];
            } else {
                //return array(message, status)
                return [
                    'message' => 'المشروع غير موجود',
                    'status' => 403,
                ];
            }
            //exception
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء تحديث المشروع: ' . $e->getMessage());
            //return array(message, status)
            return [
                'message' => 'حدث خطأ أثناء تحديث المشروع',
                'status' => 500,
            ];
        }
    }
    //**________________________________________________________________________________________________

    /**
     * function  delet   project
     * @param $id(id of project)
     * @return array(message,status)
     */

    public function deletProject($id)
    {
        try {
            //check if project is exist
            $project = Project::find($id);
            if ($project) {
                // delete project
                $project->delete();
                //return array(message, status)
                return [
                    'message' => 'تمت عملية الحذف بنجاح',
                    'status' => 200,
                ];
            } else {
                //return array(message, status)
                return [
                    'message' => 'المشروع غير موجود',
                    'status' => 403,
                ];
            }
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء الحذف المشروع: ');
            //return array(message, status)
            return [
                'message' => 'حدث خطأ أثناء الحذف المشروع',
                'status' => 500,
            ];
        }
    }
    //**________________________________________________________________________________________________
    /**
     * function  show   project ith tasks an team
     * @param $id(id of project)
     * @return array(message,project data,status)
     */
    public function showProject($id)
    {
        try {

            // Get project with taskas
            $project = Project::with('tasks', 'users')->find($id);
            // Check if project exists
            if ($project) {
                return [
                    'message' => 'تمت عملية العرض بنجاح',
                    'data' =>   $project,
                    'status' => 200,
                ];
            } else {
                return [
                    'message' => 'المشروع غير موجود',
                    'data' => 'لا توجد بيانات',
                    'status' => 403,
                ];
            }
        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء عرض المشروع: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء عرض المشروع'. $e->getMessage(),
                'data' => 'لا توجد بيانات',
                'status' => 500,
            ];
        }





    }

    /**
     * function  created to show all project related with him
     * @param nothing
     * @return array(message,project data,status)
     */
    public function showprojectUser()
    {
        try {
            $user = Auth::user();
            $projects = $user->projects;
            if(!$projects->isEmpty()) {
                return [
                    'message' => 'جميع المشاريع',
                    'data' => $projects,
                    'status' => 200,
                ];
            } else {
                return [
                    'message' => 'لم يتم تعين مشاريع لك',
                    'data' => [],
                    'status' => 200,
                ];
            }

        } catch (Exception $e) {
            Log::error('حدث خطأ أثناء عرض المشاريع: ' . $e->getMessage());
            return [
                'message' => 'حدث خطأ أثناء عرض المشاريع',
                'status' => 500,
            ];
        }
    }
}
