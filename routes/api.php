<?php

use App\Http\Controllers\Api\auth\AuthController;
use App\Http\Controllers\Api\products\CategoryController;
use App\Http\Controllers\Api\leads\LeadController;
use App\Http\Controllers\Api\logs\LoginLogController;
use App\Http\Controllers\Api\content\PageController;
use App\Http\Controllers\Api\users\PermissionController;
use App\Http\Controllers\Api\products\ProductController;
use App\Http\Controllers\Api\users\RoleController;
use App\Http\Controllers\Api\content\ServiceController;
use App\Http\Controllers\Api\settings\SettingController;
use App\Http\Controllers\Api\products\TagController;
use App\Http\Controllers\Api\upload\UploadController;
use App\Http\Controllers\Api\users\UserController;
use App\Http\Controllers\Api\logs\VisitorLogController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PageController as PublicPageController;
use App\Http\Controllers\Public\ProductController as PublicProductController;
use App\Http\Controllers\Public\ServiceController as PublicServiceController;
use Illuminate\Support\Facades\Route;

Route::get('test', function () {
    return response()->json(['message' => 'Hello World']);
});

// Public, unauthenticated API endpoints (no version prefix)
Route::prefix('openapi')->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/pages/{slug}', [PublicPageController::class, 'show']);
    Route::get('/services', [PublicServiceController::class, 'index']);
    Route::get('/services/{slug}', [PublicServiceController::class, 'show']);
    Route::get('/products', [PublicProductController::class, 'index']);
    Route::get('/products/{slug}', [PublicProductController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/settings', [SettingController::class, 'publicIndex']);
    Route::post('/contact', [ContactController::class, 'submit']);

    Route::get('test2', function () {
        return response()->json(['message' => 'Hello World 2']);
    });
});

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
            Route::apiResource('categories', CategoryController::class);
            Route::apiResource('tags', TagController::class);
        });

        // Upload routes - requires authentication
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('upload/image', [UploadController::class, 'uploadImage']);
            Route::post('upload/images', [UploadController::class, 'uploadMultipleImages']);
            Route::post('upload/file', [UploadController::class, 'uploadFile']);
            Route::delete('upload/image', [UploadController::class, 'deleteImage']);
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
