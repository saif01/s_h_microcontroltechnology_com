<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\LoginLogController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VisitorLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/public/settings', [SettingController::class, 'publicIndex']);

    // Protected admin routes - requires authentication and admin access
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);

        // Pages - requires manage-pages permission
        Route::middleware('permission:manage-pages')->group(function () {
            Route::apiResource('pages', PageController::class);
        });

        // Services - requires manage-services permission
        Route::middleware('permission:manage-services')->group(function () {
            Route::apiResource('services', ServiceController::class);
        });

        // Products - requires manage-products permission
        Route::middleware('permission:manage-products')->group(function () {
            Route::apiResource('products', ProductController::class);
        });

        // Leads - requires view-leads permission for viewing, manage-leads for updates/deletes
        Route::middleware('permission:view-leads')->group(function () {
            Route::get('leads', [LeadController::class, 'index']);
            Route::get('leads/{lead}', [LeadController::class, 'show']);
            Route::post('leads', [LeadController::class, 'store']); // Creating leads (public submission)
        });
        Route::middleware('permission:manage-leads')->group(function () {
            Route::put('leads/{lead}', [LeadController::class, 'update']);
            Route::delete('leads/{lead}', [LeadController::class, 'destroy']);
        });
        Route::middleware('permission:export-leads')->group(function () {
            Route::get('leads/export/csv', [LeadController::class, 'exportCsv']);
        });

        // Settings - requires manage-settings permission
        Route::middleware('permission:manage-settings')->group(function () {
            Route::get('settings', [SettingController::class, 'index']);
            Route::post('settings', [SettingController::class, 'update']);
        });

        // Users & Roles - requires manage-users permission
        Route::middleware('permission:manage-users')->group(function () {
            Route::get('users/roles', [UserController::class, 'roles']);
            Route::apiResource('users', UserController::class);
        });

        // Role Management - requires manage-roles permission
        Route::middleware('permission:manage-roles')->group(function () {
            Route::get('permissions', [RoleController::class, 'permissions']);
            Route::put('roles/{id}/permissions', [RoleController::class, 'syncPermissions']);
            Route::apiResource('roles', RoleController::class);
        });

        // Permission Management - requires manage-roles permission (same as roles)
        Route::middleware('permission:manage-roles')->group(function () {
            Route::get('permissions/groups', [PermissionController::class, 'groups']);
            Route::post('permissions/groups/rename', [PermissionController::class, 'renameGroup']);
            Route::post('permissions/groups/delete', [PermissionController::class, 'deleteGroup']);
            Route::apiResource('permissions', PermissionController::class);
        });

        // Login Logs - requires view-login-logs permission
        Route::middleware('permission:view-login-logs')->group(function () {
            Route::get('login-logs/statistics', [LoginLogController::class, 'statistics']);
            Route::apiResource('login-logs', LoginLogController::class)->only(['index', 'show', 'destroy']);
        });

        // Visitor Logs - requires view-visitor-logs permission
        Route::middleware('permission:view-visitor-logs')->group(function () {
            Route::get('visitor-logs/statistics', [VisitorLogController::class, 'statistics']);
            Route::post('visitor-logs/delete-multiple', [VisitorLogController::class, 'destroyMultiple']);
            Route::apiResource('visitor-logs', VisitorLogController::class)->only(['index', 'show', 'destroy']);
        });
    });
});
