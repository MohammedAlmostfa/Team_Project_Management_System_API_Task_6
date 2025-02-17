<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ProjectFormRequestCreat;
use App\Http\Requests\Project\ProjectFormRequestUpdate;
use App\Http\Resources\ProjectResource;
use App\Service\ProjectService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Show all projects with tasks
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $result = $this->projectService->showallProjects($request);
        return self::paginated($result['data'], ProjectResource::class, $result['message'], $result['status']);

    }

    /**
     * Create a new project
     * @param ProjectFormRequestCreat $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProjectFormRequestCreat $request)
    {
        $validatedData = $request->validated();
        $result = $this->projectService->createProject($validatedData);
        return $result['status'] === 200
           ? self::success(new ProjectResource($result['data']), $result['message'], $result['status'])
            : self::error(new ProjectResource($result['data']), $result['message'], $result['status']);

    }

    /**
     * Show a project by ID
     * @param string $id (ID of the project)
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $result = $this->projectService->showProject($id);
        return self::success(new ProjectResource($result['data']), $result['message'], $result['status']);
    }

    /**
     * Update a project
     * @param ProjectFormRequestUpdate $request
     * @param string $id (ID of the project)
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProjectFormRequestUpdate $request, string $id)
    {
        $validatedData = $request->validated();
        $result = $this->projectService->updateProject($validatedData, $id);
        return $result['status'] === 200
        ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Delete a project by ID
     * @param string $id (ID of the project)
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $result = $this->projectService->deleteProject($id);
        return self::success(null, $result['message'], $result['status']);
    }

    /**
     * Show projects related to the authenticated user
     * @return \Illuminate\Http\JsonResponse
     */
    public function showProjectUser()
    {
        $result = $this->projectService->showprojectUser();
        return self::paginated($result['data'], ProjectResource::class, $result['message'], $result['status']);
    }

}
