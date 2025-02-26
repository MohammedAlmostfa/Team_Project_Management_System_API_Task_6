<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Service\TeamService;
use App\Http\Resources\TeamResource;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Team\TeamFormRequestCreat;
use App\Http\Requests\Team\TeamFormRequestUpdate;
use Illuminate\Auth\Access\AuthorizationException;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Create a new team.
     *
     * @param TeamFormRequestCreate $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TeamFormRequestCreat $request, string $id)
    {
        $project = Project::findOrFail($id);

        if (Gate::denies('createTeam', $project)) {
            throw new AuthorizationException('A team has already been appointed to this project.');
        }


        $validatedData = $request->validated();
        $result = $this->teamService->createTeam($validatedData, $id);

        return $result['status'] === 200
            ? $this->success(new TeamResource($result['data']), $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Update the specified team.
     *
     * @param TeamFormRequestUpdate $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TeamFormRequestUpdate $request, string $id)
    {

        $project = Project::findOrFail($id);

        if (Gate::denies('updateTeam', $project)) {
            throw new AuthorizationException('No team has been appointed to this project yet.');
        }

        $validatedData = $request->validated();
        $result = $this->teamService->updateTeam($validatedData, $id);

        return $result['status'] === 200
            ? $this->success(null, $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Delete the specified team.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);

        if (Gate::denies('deleteTeam', $project)) {
            throw new AuthorizationException('No team has been appointed to this project yet.');
        }

        $result = $this->teamService->deleteTeam($id);

        return $result['status'] === 200
            ? $this->success(null, $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }
}
