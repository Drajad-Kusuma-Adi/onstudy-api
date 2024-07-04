<?php

use App\Http\Controllers\AnswersController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\SubmissionsController;
use App\Http\Controllers\UserClassroomController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v2')->group(function() {
    // User endpoints
    Route::prefix('/users')->group(function() {
        Route::post('/register', [UserController::class, 'register']);
        Route::post('/login', [UserController::class, 'login']);
        Route::get('/verify', [UserController::class, 'verify']);
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/update_profile', [UserController::class, 'update_profile']);
    });

    // Classroom endpoints
    Route::prefix('/classrooms')->group(function() {
        Route::post('/create_classroom', [ClassroomController::class, 'create_classroom']);
        Route::post('/join_classroom', [ClassroomController::class, 'join_classroom']);
        Route::post('/get_classrooms_by_user_id', [ClassroomController::class, 'get_classrooms_by_user_id']);
    });
});
