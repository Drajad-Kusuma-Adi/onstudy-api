<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use App\Models\Submission;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Auth
Route::post('/v1/auth/register', [AuthController::class, 'register']);
Route::post('/v1/auth/login', [AuthController::class, 'login']);
Route::post('/v1/auth/verifyauth', [AuthController::class, 'verifyAuth']);
Route::post('/v1/auth/logout', [AuthController::class, 'logout']);
Route::post('/v1/auth/oauth', [AuthController::class, 'oauth']);

// Classroom REST API
Route::post('/v1/classrooms', [ClassroomController::class, 'create']);
Route::get('/v1/classrooms', [ClassroomController::class, 'read']);
Route::get('/v1/classrooms/{id}', [ClassroomController::class, 'readById']);
Route::post('/v1/classrooms/{id}', [ClassroomController::class, 'update']);
Route::delete('/v1/classrooms/{id}', [ClassroomController::class, 'delete']);

// User REST API
Route::get('/v1/users', [UserController::class, 'read']);
Route::get('/v1/users/{id}', [UserController::class, 'readById']);
Route::post('/v1/users/{id}', [UserController::class, 'update']);
Route::delete('/v1/users/{id}', [UserController::class, 'delete']);

// Material REST API
Route::post('/v1/materials', [MaterialController::class, 'create']);
Route::get('/v1/materials', [MaterialController::class, 'read']);
Route::get('/v1/materials/{id}', [MaterialController::class, 'readById']);
Route::post('/v1/materials/{id}', [MaterialController::class, 'update']);
Route::delete('/v1/materials/{id}', [MaterialController::class, 'delete']);
// Submissions REST API
Route::post('/v1/submissions', [SubmissionController::class, 'create']);
Route::get('/v1/materials/{materialId}/submissions', [SubmissionController::class, 'read']);
Route::get('/v1/submissions/{id}', [SubmissionController::class, 'readById']);
Route::post('/v1/submissions/{id}', [SubmissionController::class, 'update']);
Route::delete('/v1/submissions/{id}', [SubmissionController::class, 'delete']);
