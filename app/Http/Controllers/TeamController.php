<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamFormRequset;
use App\Models\Project;
use App\Service\TeamService;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }


    /**
     **create team
     **@parm TeamFormRequset request
     **@parm id(id op project)
     **return response()->json(message)
     */
    public function store(TeamFormRequset $request, string $id)
    {
        //valdated data
        $validatedData = $request->validated();
        //create team
        $result = $this->teamService->createTeam($validatedData, $id);
        // return  response
        return response()->json([
            'message' => $result['message'],
        ], $result['status']);
    }


    public function show(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeamFormRequset $request, string $id)
    {
        $validatedData = $request->validated();
        $result = $this->teamService->updateTeam($validatedData, $id);
        return response()->json([
            'message' => $result['message'],

        ], $result['status']);
    }


    /**
     **delet team
     **@parm id(id op project)
     **return response()->json(message)
     */

    public function destroy(string $id)
    {
        $result = $this->teamService->deleteTeam($id);
        return response()->json([
            'message' => $result['message'],
        ], $result['status']);
    }
}
