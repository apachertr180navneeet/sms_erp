<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\SchoolController;
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
Route::apiResource('plans', PlanController::class)->only(['index', 'show']);

Route::middleware(['auth:sanctum', 'role:super_admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Super Admin Dashboard']);
    });

    Route::get('/schools/stats', [SchoolController::class, 'stats']);
    Route::apiResource('schools', SchoolController::class);
    Route::apiResource('plans', PlanController::class);
});

Route::middleware(['auth:sanctum', 'role:school_admin'])->prefix('school')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'School Admin Dashboard']);
    });
});
