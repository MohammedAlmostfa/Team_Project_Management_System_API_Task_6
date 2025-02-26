<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\UserService;
use App\Http\Requests\User\UserFortmRequestCreat;
use App\Http\Requests\User\UserFortmRequestUpdate;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    protected $userService;

    // Constructor to inject UserService
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of all users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $result = $this->userService->showUsers();
        return $result['status'] === 200
            ? $this->success(new UserResource($result['data']), $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param UserFortmRequestCreat $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserFortmRequestCreat $request)
    {
        $validatedData = $request->validated();
        $result = $this->userService->createUser($validatedData);
        return $result['status'] === 200
            ? $this->success(new UserResource($result['data']), $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Update the specified user in storage.
     *
     * @param UserFortmRequestUpdate $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserFortmRequestUpdate $request, $id)
    {
        $validatedData = $request->validated();
        $result = $this->userService->updateUser($validatedData, $id);
        return $result['status'] === 200
            ? $this->success(new UserResource($result['data']), $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $result = $this->userService->deletUser($id);
        return $result['status'] === 200
            ? $this->success(null, $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Restore the specified user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function returnUser($id)
    {
        $result = $this->userService->returnUser($id);
        return $result['status'] === 200
            ? $this->success(new UserResource($result['data']), $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $result = $this->userService->showUser($id);
        return $result['status'] === 200
            ? $this->success(new UserResource($result['data']), $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }
    /**
 * Display a list of soft-deleted users.
 *
 * @return \Illuminate\Http\JsonResponse
 */
    public function showDeletedUsers()
    {
        $result = $this->userService->showDeletedUsers();

        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }


}
