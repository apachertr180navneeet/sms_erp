<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SchoolRegistrationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

Route::post('/schools/register', [SchoolRegistrationController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('admin')->middleware('role:super_admin')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'Super Admin Dashboard']);
        });
    });

    Route::prefix('school')->middleware('role:school_admin')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'School Admin Dashboard']);
        });
    });
});
