<?php

use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v2/users')->group(function () {
    // Auth endpoints
    Route::post('/auth', [UserController::class, 'auth']);
    Route::get('/verify', [UserController::class, 'verify']);
    Route::get('/logout', [UserController::class, 'logout']);

    // User endpoints
    Route::post('/profile', [UserController::class, 'update_profile']);
});

Route::prefix('v2/classrooms')->group(function () {
    // Classroom endpoints
    Route::post('/create', [ClassroomController::class, 'create_classroom']);
    Route::get('/read', [ClassroomController::class, 'read_classroom_by_id']);
    Route::post('/update', [ClassroomController::class, 'update_classroom']);
    Route::post('/delete', [ClassroomController::class, 'delete_classroom']);
});
