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

Route::prefix('v1')->group(function() {
    // User endpoints
    Route::prefix('/users')->group(function() {
        Route::post('/register', [UserController::class, 'register']);
        Route::post('/login', [UserController::class, 'login']);
        Route::get('/verify', [UserController::class, 'verify']);
        Route::get('/get_user_by_id/{user_id}', [UserController::class, 'get_user_by_id']);
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/update_profile', [UserController::class, 'update_profile']);
    });

    // Classroom endpoints
    Route::prefix('/classrooms')->group(function() {
        Route::post('/create_classroom', [ClassroomController::class, 'create_classroom']);
        Route::post('/join_classroom', [ClassroomController::class, 'join_classroom']);
        Route::get('/get_classrooms_by_user_id/{user_id}', [ClassroomController::class, 'get_classrooms_by_user_id']);
        Route::get('/get_members_by_classroom_id/{classroom_id}', [ClassroomController::class, 'get_members_by_classroom_id']);
    });

    // Assignment endpoints
    Route::prefix('/assignments')->group(function() {
        Route::post('/create_full_assignment', [AssignmentsController::class, 'create_full_assignment']);
        Route::get('/get_full_assignments_by_classroom_id/{classroom_id}', [AssignmentsController::class, 'get_full_assignments_by_classroom_id']);
    });

    // Submission endpoints
    Route::prefix('/submissions')->group(function() {
        Route::post('/create_submission', [SubmissionsController::class, 'create_submission']);
        Route::get('/check_submission/{assignment_id}/{user_id}', [SubmissionsController::class, 'check_submission']);
        Route::get('/get_submitters_by_assignment_id/{assignment_id}', [SubmissionsController::class, 'get_submitters_by_assignment_id']);
        Route::get('/get_submissions_by_user_id_with_status/{user_id}', [SubmissionsController::class, 'get_submissions_by_user_id_with_status']);
        Route::get('/get_avg_grade_by_user_id/{user_id}', [SubmissionsController::class, 'get_avg_grade_by_user_id']);
    });
});
