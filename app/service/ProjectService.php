<?php

namespace App\Service;

use App\Models\Project;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectService
{

    /**
     * Function created to show all projects
     * @param array $data
     * @return array(message, project data, status)
     */
    public function showallProjects($data)
    {
        try {
            if ($data['status']) {
                // Get all projects with latest and oldest tasks
                $projects = Project::with('lastoftask', 'oldoftask')->status($data['status'])->paginate(10);
            } else {
                $projects = Project::with('lastoftask', 'oldoftask')->paginate(10);
            }
            // Return array(message, project data, status)
            return [
                'message' => 'Projects retrieved successfully',
                'data' => $projects,
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('Error while retrieving projects: ' . $e->getMessage());
            // Return array(message, project data, status)
            return [
                'message' => 'An error occurred while retrieving projects',
                'data' => 'No data available',
                'status' => 500,
            ];
        }
    }

    /**
     * Function created to create a new project
     * @param array $data (data to be inserted)
     * @return array(message, project data, status)
     */
    public function createProject($data)
    {
        try {
            // Create project
            $project = Project::create($data);
            // Return array(message, project data, status)
            return [
                'message' => 'Project created successfully',
                'data' => $project,
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('Error while creating project: ' . $e->getMessage());
            // Return array(message, project data, status)
            return [
                'message' => 'An error occurred while creating the project',
                'data' => 'No data available',
                'status' => 500,
            ];
        }
    }

    /**
     * Function created to update a project
     * @param array $data (data to be updated)
     * @param int $id (id of the project)
     * @return array(message, status)
     */
    public function updateProject($data, $id)
    {
        try {
            // Update project
            $project = Project::find($id);
            // Check if project exists
            if ($project) {
                $project->update([
                    'name' => $data['name'] ?? $project->name,
                    'description' => $data['description'] ?? $project->description,
                ]);
                // Return array(message, status)
                return [
                    'message' => 'Project updated successfully',
                    'status' => 200,
                ];
            } else {
                // Return array(message, status)
                return [
                    'message' => 'Project not found',
                    'status' => 403,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error while updating project: ' . $e->getMessage());
            // Return array(message, status)
            return [
                'message' => 'An error occurred while updating the project',
                'status' => 500,
            ];
        }
    }

    /**
     * Function created to delete a project
     * @param int $id (id of the project)
     * @return array(message, status)
     */
    public function deleteProject($id)
    {
        try {
            // Check if project exists
            $project = Project::find($id);
            if ($project) {
                // Delete project
                $project->delete();
                // Return array(message, status)
                return [
                    'message' => 'Project deleted successfully',
                    'status' => 200,
                ];
            } else {
                // Return array(message, status)
                return [
                    'message' => 'Project not found',
                    'status' => 403,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error while deleting project: ' . $e->getMessage());
            // Return array(message, status)
            return [
                'message' => 'An error occurred while deleting the project',
                'status' => 500,
            ];
        }
    }

    /**
     * Function created to show a project with its tasks and team
     * @param int $id (id of the project)
     * @return array(message, project data, status)
     */
    public function showProject($id)
    {
        try {
            // Get project with tasks and users
            $project = Project::with('tasks', 'users')->find($id);
            // Check if project exists
            if ($project) {
                return [
                    'message' => 'Project retrieved successfully',
                    'data' => $project,
                    'status' => 200,
                ];
            } else {
                return [
                    'message' => 'Project not found',
                    'data' => 'No data available',
                    'status' => 403,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error while retrieving project: ' . $e->getMessage());
            return [
                'message' => 'An error occurred while retrieving the project',
                'data' => 'No data available',
                'status' => 500,
            ];
        }
    }

    /**
     * Function created to show all projects related to the authenticated user
     * @return array(message, project data, status)
     */
    public function showProjectUser()
    {
        try {
            $user = Auth::user();
            $projects = $user->projects->paginate(10);
            if (!$projects->isEmpty()) {
                return [
                    'message' => 'All projects retrieved successfully',
                    'data' => $projects,
                    'status' => 200,
                ];
            } else {
                return [
                    'message' => 'No projects assigned to you',
                    'data' => [],
                    'status' => 200,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error while retrieving user projects: ' . $e->getMessage());
            return [
                'message' => 'An error occurred while retrieving projects',
                'status' => 500,
            ];
        }
    }
}
