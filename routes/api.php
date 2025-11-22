<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/login', [AuthController::class, 'login']);
    
    // Protected admin routes
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);
        
        // Pages
        Route::apiResource('pages', PageController::class);
        
        // Services
        Route::apiResource('services', ServiceController::class);
        
        // Products
        Route::apiResource('products', ProductController::class);
        
        // Leads
        Route::apiResource('leads', LeadController::class);
        Route::get('leads/export/csv', [LeadController::class, 'exportCsv']);
        
        // Settings
        Route::get('settings', [SettingController::class, 'index']);
        Route::post('settings', [SettingController::class, 'update']);
        
        // Users & Roles
        Route::get('users/roles', [UserController::class, 'roles']);
        Route::apiResource('users', UserController::class);
    });
});

