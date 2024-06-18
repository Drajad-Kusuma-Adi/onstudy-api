<?php

use App\Http\Controllers\AnswersController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\SubmissionsController;
use App\Http\Controllers\UserClassroomController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User endpoints
Route::prefix('v2/users')->group(function() {
    // Auth endpoints
    Route::post('/auth', [UserController::class, 'auth']);
    Route::get('/verify', [UserController::class, 'verify']);
    Route::get('/logout', [UserController::class, 'logout']);

    // User endpoints
    Route::get('/profile', [UserController::class, 'regular_read_by_id']);
    Route::post('/profile', [UserController::class, 'update_profile']);
    Route::post('/delete', [UserController::class, 'regular_delete']);
});

// Define regular CRUD endpoints
function defineCrudRoutes($prefix, $controller) {
    Route::prefix("v2/$prefix")->group(function() use ($controller) {
        Route::post('/create', [$controller, 'regular_create']);
        Route::get('/read', [$controller, 'regular_read_by_id']);
        Route::post('/update', [$controller, 'regular_update']);
        Route::post('/delete', [$controller, 'regular_delete']);
    });
}

// Define routes for each resources
defineCrudRoutes('classrooms', ClassroomController::class);
defineCrudRoutes('user_classrooms', UserClassroomController::class);
defineCrudRoutes('assignments', AssignmentsController::class);
defineCrudRoutes('questions', QuestionsController::class);
defineCrudRoutes('answers', AnswersController::class);
defineCrudRoutes('submissions', SubmissionsController::class);

// Specific endpoints for specific needs
Route::prefix('v2/classrooms')->group(function() {
    Route::get('/find_teacher', [ClassroomController::class, 'find_teacher']);
});
Route::prefix('v2/user_classrooms')->group(function() {
    Route::get('/read_by_user_id', [UserClassroomController::class, 'read_user_classroom_by_user_id']);
    Route::get('/read_by_classroom_id', [UserClassroomController::class, 'read_user_classroom_by_classroom_id']);
});
Route::prefix('v2/assignments')->group(function() {
    Route::get('/read_by_classroom_id', [AssignmentsController::class, 'read_by_classroom_id']);
});
Route::prefix('v2/questions')->group(function() {
    Route::get('/read_by_assignment_id', [QuestionsController::class, 'read_by_assignment_id']);
});
Route::prefix('v2/answers')->group(function() {
    Route::get('/read_by_question_id', [AnswersController::class, 'read_by_question_id']);
});
Route::prefix('v2/submissions')->group(function() {
    Route::get('/read_by_assignment_id', [SubmissionsController::class, 'read_by_assignment_id']);
});
