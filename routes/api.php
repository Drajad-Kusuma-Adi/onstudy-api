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

Route::prefix('v1/classrooms')->group(function () {
    Route::get('/', [ClassroomController::class, 'read']);
    Route::get('/{id}', [ClassroomController::class, 'readById']);
    Route::post('/', [ClassroomController::class, 'create']);
    Route::put('/{id}', [ClassroomController::class, 'update']);
    Route::delete('/{id}', [ClassroomController::class, 'delete']);
});

Route::prefix('v1/users')->group(function () {
    Route::get('/', [UserController::class, 'read']);
    Route::get('/{id}', [UserController::class, 'readById']);
    Route::post('/', [UserController::class, 'create']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'delete']);
});

Route::prefix('v1/materials')->group(function () {
    Route::get('/', [MaterialController::class, 'read']);
    Route::get('/{id}', [MaterialController::class, 'readById']);
    Route::post('/', [MaterialController::class, 'create']);
    Route::put('/{id}', [MaterialController::class, 'update']);
    Route::delete('/{id}', [MaterialController::class, 'delete']);
});

Route::prefix('v1/submissions')->group(function () {
    Route::get('/material/{materialId}', [SubmissionController::class, 'read']);
    Route::get('/{id}', [SubmissionController::class, 'readById']);
    Route::post('/', [SubmissionController::class, 'create']);
    Route::put('/{id}', [SubmissionController::class, 'update']);
    Route::delete('/{id}', [SubmissionController::class, 'delete']);
});
