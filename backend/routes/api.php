<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\SchoolRegistrationController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UserController;
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
    Route::get('/schools/{school}/modules', [SubscriptionController::class, 'schoolModules']);
    Route::put('/schools/{school}/modules', [SubscriptionController::class, 'updateSchoolModules']);
    Route::apiResource('schools', SchoolController::class);

    Route::apiResource('plans', PlanController::class);

    Route::apiResource('modules', ModuleController::class);
    Route::post('/modules/assign', [ModuleController::class, 'assignToSchool']);

    Route::get('/subscriptions/stats', [SubscriptionController::class, 'stats']);
    Route::post('/subscriptions/check-expiry', [SubscriptionController::class, 'checkExpiry']);
    Route::apiResource('subscriptions', SubscriptionController::class);

    Route::get('/users/stats', [UserController::class, 'stats']);
    Route::put('/users/{user}/role', [UserController::class, 'assignRole']);
    Route::apiResource('users', UserController::class);
});

Route::middleware(['auth:sanctum', 'role:school_admin', 'tenant'])->prefix('school')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'School Admin Dashboard']);
    });

    Route::apiResource('students', \App\Http\Controllers\Api\StudentController::class);
    Route::apiResource('classes', \App\Http\Controllers\Api\SchoolClassController::class);
    Route::apiResource('sections', \App\Http\Controllers\Api\SectionController::class);
    Route::apiResource('subjects', \App\Http\Controllers\Api\SubjectController::class);
    Route::apiResource('teachers', \App\Http\Controllers\Api\TeacherController::class);
});
