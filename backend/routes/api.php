<?php

use App\Http\Controllers\Api\AccessRuleController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());

    Route::get('/profile',  [ProfileController::class, 'show']);
    Route::put('/profile',  [ProfileController::class, 'update']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::middleware('is_admin')->group(function () {

        Route::apiResource('applications', ApplicationController::class);
        Route::apiResource('categories', CategoryController::class);

        Route::get('/access-rules/{type}/{id}', [AccessRuleController::class, 'show']);
        Route::put('/access-rules/{type}/{id}', [AccessRuleController::class, 'update']);

        Route::get('/reports/user-access', [ReportController::class, 'userAccess']);

        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('roles',       RoleController::class);
        Route::apiResource('users',       UserController::class);
    });
});
