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

// User endpoints
Route::prefix('v2/users')->group(function() {
    // Auth endpoints
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/verify', [UserController::class, 'verify']);
    Route::delete('/logout', [UserController::class, 'logout']);
});
