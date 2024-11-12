<?php

namespace App\Http\Controllers;

use App\Http\Requests\projectFormRequest;
use App\Service\projectService;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(projectService $projectService)
    {
        $this->projectService = $projectService;
    }
    //**________________________________________________________________________________________________
    /**
   *show all projects with tasks
   * @param nothing
   * @return response json(message,project data)
   */
    public function index(Request $request)
    {
        //  $validatedData = $request->validated();
        $result = $this->projectService->showallProjects($request);
        return Response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['status']);
    }
    //**________________________________________________________________________________________________

    /**
     * create a new project
     * @param projectFormRequest
     * @return response json(message,project data)
     */
    public function store(projectFormRequest $request)
    {
        $validatedData = $request->validated();
        $result = $this->projectService->createProject($validatedData);
        return Response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['status']);
    }
    //**________________________________________________________________________________________________
    /**
     * show  project by id
     * @param id(id of project)
     * @return response json(message,project data)
     */
    public function show(string $id)
    {
        $result = $this->projectService->showProject($id);
        return Response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['status']);
    }
    //**________________________________________________________________________________________________
    /**
      * update a  project
      * @param projectFormRequest
      * @parm id(id of project)
      * @return response json(message)
      */
    public function update(projectFormRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $result = $this->projectService->updateProject($validatedData, $id);
        return Response()->json([
            'message' => $result['message'],
        ], $result['status']);
    }
    //**________________________________________________________________________________________________

    /**
 * delet  project by id
 * @param id(id of project)
 * @return response json(message)
 */

    public function destroy(string $id)
    {

        $result = $this->projectService->deletProject($id);
        return Response()->json([
            'message' => $result['message'],
        ], $result['status']);
    }

    //**________________________________________________________________________________________________
    /**
          * show project relater to user


          * @return response json(message,dtaa)
          */

    public function showproject()
    {
        //create team
        $result = $this->projectService->showprojectUser();
        // return  response
        return response()->json([
            'message' => $result['message'],
             'data' => $result['data'],
        ], $result['status']);
    }


}
