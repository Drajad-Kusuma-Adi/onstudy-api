<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v2/users')->group(function () {
    // Auth Endpoints
    Route::post('/auth', [UserController::class, 'auth']);
    Route::get('/verify', [UserController::class, 'verify']);
    Route::get('/logout', [UserController::class, 'logout']);
});
