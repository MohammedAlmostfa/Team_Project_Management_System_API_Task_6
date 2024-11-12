<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskFormRequset;
use Illuminate\Http\Request;
use App\Service\TaskService;

class TaskController extends Controller
{

    protected $taskService;
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }


    public function index()
    {

        /**
         ** show  task of project
         **return response  jsonc ontainingmessage,data of task
         */
        $result=$this->taskService->showallTask();
        return Response()->json([
                   'message' => $result['message'],
                   'data' => $result['data'],
               ], $result['status']);
    }
    /**
     * * create task
       ** @parm TaskFormRequset request
       **return response  jsonc ontainingmessage,data of task
       */
    public function store(TaskFormRequset $request)
    {
        $validatedData=$request->validated();
        $result=$this->taskService->createTask($validatedData);
        return Response()->json([
                   'message' => $result['message'],
                   'data' => $result['data'],
               ], $result['status']);
    }

    /**
 * *Function to show task
 * *@param int $id ID of the task
 * *@return array Response json   containing messag,data of task
 */

    public function show(string $id)
    {
        $result=$this->taskService->showTask($id);
        return Response()->json([
                   'message' => $result['message'],
                   'data' => $result['data'],
               ], $result['status']);
    }


    /**
      ** Function to updat task
      ** @param int $id ID of the task
     **@param TaskFormRequset request
      ** @return array Response  json  containing message and task
      **/

    public function update(TaskFormRequset $request, string $id)
    {
        $validatedData=$request->validated();
        $result=$this->taskService->updateTask($validatedData, $id);
        return Response()->json([
                   'message' => $result['message'],
                   'data' => $result['data'],
               ], $result['status']);
    }

    /**
     ** Function to delete task
     ** @param int $id ID of the task
     ** @return array Response json  array containing messag
     */

    public function destroy(string $id)
    {

        $result=$this->taskService->deleteTask($id);
        return Response()->json([
                   'message' => $result['message'],
               ], $result['status']);
    }


    public function showtask(TaskFormRequset $request)
    {
        $validatedData=$request->validated();

        $result=$this->taskService->show_his_tasks($validatedData);
        return Response()->json([
                   'message' => $result['message'],
                   'data' => $result['data'],
               ], $result['status']);
    }


}
