<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;

// for all user
Route::controller(AuthController::class)->group(function () {
    // login
    Route::post('/login', 'login');
    //logout
    Route::post('/logout', 'logout');
    // refresh
    Route::post('/refresh', 'refresh');
});

Route::get('project/{id}', [ProjectController::class, 'show'])->middleware('checkRole:user,admin');


Route::group(['middleware' => ['checkRole:admin']], function () {
    //show all user
    Route::resource('/User', UserController::class);
    //return user
    Route::post('/User/{id}', [UserController::class, 'returnuser']);
    // project routs
    Route::resource('project', ProjectController::class);
    //add tam
    Route::post('Team/{id}', [TeamController::class,'store']);

    Route::delete('Team/{id}', [TeamController::class,'destroy']);
    // updat team
    Route::put('Team/{id}', [TeamController::class,'update']);

});
Route::group(['middleware' => ['checkRole:user']], function () {
    // get tasks of his team
    Route::get('Task/', [TaskController::class,'index']);
    // user sho all of tasks
    Route::get('Tasks/', [TaskController::class,'showtask']);
    // update task data
    Route::put('Task/{id}', [TaskController::class,'update']);
    // delet task
    Route::delete('Task/{id}', [TaskController::class,'destroy']);
    //show task data
    Route::get('Task/{id}', [TaskController::class,'show']);

    Route::get('myproject/', [ProjectController::class,'showproject']);
    // creat tasks
    Route::group(['middleware' => ['checkUserRole:Manger']], function () {
        Route::post('Task', [TaskController::class,'store']);
    });

});
