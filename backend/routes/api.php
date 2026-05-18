<?php

use App\Http\Controllers\Api\AccessRuleController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ────────────────────────────────────────────────────────────

Route::post('/login', [AuthController::class, 'login']);

// ─── Protected Routes (Sanctum) ──────────────────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn (Request $request) => $request->user());

    // Profile — any authenticated user
    Route::get('/profile',  [ProfileController::class, 'show']);
    Route::put('/profile',  [ProfileController::class, 'update']);

    // Dashboard — accessible by ALL authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ─── Admin-only Routes ────────────────────────────────────────────────────
    Route::middleware('is_admin')->group(function () {

        // Master Applications — full CRUD
        Route::apiResource('applications', ApplicationController::class);

        // Access Rules
        Route::get('/access-rules/{type}/{id}', [AccessRuleController::class, 'show']);
        Route::put('/access-rules/{type}/{id}', [AccessRuleController::class, 'update']);

        // Master Departments, Roles, Users — full CRUD
        Route::apiResource('departments', DepartmentController::class)->except(['show']);
        Route::apiResource('roles',       RoleController::class)->except(['show']);
        Route::apiResource('users',       UserController::class)->except(['show']);
    });
});
