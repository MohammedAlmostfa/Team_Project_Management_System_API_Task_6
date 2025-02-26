<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Service\TaskService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\TaskResource;
use App\Http\Requests\TaskFormRequset;
use App\Http\Requests\Task\TaskFormRequestCreat;
use App\Http\Requests\Task\TaskFormRequestUpdate;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display all tasks.
     */
    public function index(): JsonResponse
    {
        $result = $this->taskService->showAllTasks();

        return $result['status'] === 200
            ? self::paginated($result['data'], TaskResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Create a new task.
     */
    public function store(TaskFormRequestCreat $request): JsonResponse
    {
        $project = Project::find($request->project_id);
        $this->authorize('create', $project);

        $validatedData = $request->validated();
        $result = $this->taskService->createTask($validatedData);

        return $result['status'] === 200
            ? self::success(new TaskResource($result['data']), $result['message'], $result['status'])
            : self::error(new TaskResource($result['data']), $result['message'], $result['status']);
    }

    /**
     * Display a specific task.
     */
    public function show(string $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        $this->authorize('showTask', $task);

        $result = $this->taskService->showTask($id);

        return $result['status'] === 200
            ? self::success(new TaskResource($result['data']), $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Update a task as a manager.
     */
    public function MangerUpdateTask(TaskFormRequestUpdate $request, string $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        $project = $task->project;
        $this->authorize('updateAsManger', $project);

        $validatedData = $request->validated();
        $result = $this->taskService->ManagerUpdateTask($validatedData, $id);

        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Update a task as a regular user.
     */
    public function UserUpdateTask(TaskFormRequestCreat $request, string $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        $this->authorize('updateAsUser', $task);

        $validatedData = $request->validated();
        $result = $this->taskService->UserUpdateTask($validatedData, $id);

        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Update a task as a tester.
     */
    public function TasterUpdateTask(TaskFormRequestUpdate $request, string $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        $project = $task->project;
        $this->authorize('updateAsTaster', $project);

        $validatedData = $request->validated();
        $result = $this->taskService->TasterUpdateTask($validatedData, $id);

        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Delete a task.
     */
    public function destroy(string $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        $project = $task->project;
        $this->authorize('deleteTask', $project);

        $result = $this->taskService->deleteTask($id);

        return $result['status'] === 200
            ? self::success(null, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }

    /**
     * Display tasks assigned to the authenticated user.
     */
    public function showUserTasks(TaskFormRequset $request)
    {
        $validatedData = $request->validated();
        $result = $this->taskService->showHisTasks($validatedData);

        return $result['status'] === 200
            ? self::paginated($result['data'], TaskResource::class, $result['message'], $result['status'])
            : self::error(null, $result['message'], $result['status']);
    }
}
