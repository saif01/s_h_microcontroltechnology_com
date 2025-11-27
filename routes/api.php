<?php

use App\Http\Controllers\Api\auth\AuthController;
use App\Http\Controllers\Api\products\CategoryController;
use App\Http\Controllers\Api\leads\LeadController;
use App\Http\Controllers\Api\logs\LoginLogController;
use App\Http\Controllers\Api\users\PermissionController;
use App\Http\Controllers\Api\products\ProductController;
use App\Http\Controllers\Api\users\RoleController;
use App\Http\Controllers\Api\service\ServiceController;
use App\Http\Controllers\Api\settings\SettingController;
use App\Http\Controllers\Api\products\TagController;
use App\Http\Controllers\Api\upload\UploadController;
use App\Http\Controllers\Api\users\UserController;
use App\Http\Controllers\Api\logs\VisitorLogController;
use App\Http\Controllers\Api\newsletters\NewsletterController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Public\NewsletterController as PublicNewsletterController;
use App\Http\Controllers\Public\pages\ContactController;
use App\Http\Controllers\Public\pages\HomeController;
use App\Http\Controllers\Public\pages\PageController as PublicPageController;
use App\Http\Controllers\Public\products\ProductController as PublicProductController;
use App\Http\Controllers\Public\services\ServiceController as PublicServiceController;
use App\Http\Controllers\Public\about\AboutController as PublicAboutController;
use App\Http\Controllers\Public\blog\BlogController as PublicBlogController;
use App\Http\Controllers\Api\about\AboutController;
use App\Http\Controllers\Api\blog\BlogController;
use App\Http\Controllers\Api\career\CareerController;
use App\Http\Controllers\Api\career\JobApplicationController;
use Illuminate\Support\Facades\Route;

Route::get('test', function () {
    return response()->json(['message' => 'Hello World']);
});

// Test Telegram Notification
Route::get('test/telegram', function () {
    try {
        $message = request()->get('message', null);
        $chatId = request()->get('chat_id', null);
        
        $result = \App\Services\TelegramNotify::T_NOTIFY($message, $chatId);
        
        if ($result === false) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram notification failed. Check logs for details.',
                'config' => [
                    'bot_token_configured' => !empty(config('telegram.bots.mybot.token')) || !empty(env('TELEGRAM_BOT_TOKEN')),
                    'chat_id_configured' => !empty(config('values.telegram_chat_id')) || !empty(env('TELEGRAM_CHAT_ID')),
                ]
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Telegram notification sent successfully!',
            'response' => [
                'message_id' => $result->messageId ?? $result->get('message_id'),
                'chat_id' => $result->chat->id ?? $result->get('chat')->get('id'),
                'date' => $result->date ?? $result->get('date'),
                'text' => $result->text ?? $result->get('text'),
            ]
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
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
    Route::get('/about', [PublicAboutController::class, 'index']);
    
    // Blog routes - specific routes must come before parameterized routes
    Route::get('/blog', [PublicBlogController::class, 'index']);
    Route::get('/blog/categories', [PublicBlogController::class, 'categories']);
    Route::get('/blog/{slug}', [PublicBlogController::class, 'show']);
    
    // Career routes
    Route::get('/careers', [\App\Http\Controllers\Public\career\CareerController::class, 'index']);
    Route::get('/careers/{slug}', [\App\Http\Controllers\Public\career\CareerController::class, 'show']);
    Route::post('/careers/apply', [\App\Http\Controllers\Public\career\CareerController::class, 'apply']);
    
    Route::post('/contact', [ContactController::class, 'submit']);
    Route::post('/newsletter/subscribe', [PublicNewsletterController::class, 'subscribe']);

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

        // Dashboard - no permissions required, only authentication
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // About page management (singleton - only one record) - requires manage-about permission
        Route::middleware('permission:manage-about')->group(function () {
            Route::get('about', [AboutController::class, 'index']);
            Route::post('about', [AboutController::class, 'store']);
            Route::put('about', [AboutController::class, 'update']);
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

        // Blog Posts - requires manage-blog permission
        Route::middleware('permission:manage-blog')->group(function () {
            Route::apiResource('blog-posts', BlogController::class);
            Route::apiResource('blog-categories', \App\Http\Controllers\Api\blog\BlogCategoryController::class);
        });

        // Careers - requires manage-careers permission
        Route::middleware('permission:manage-careers')->group(function () {
            Route::apiResource('careers', CareerController::class);
        });

        // Job Applications - requires manage-applications permission
        Route::middleware('permission:manage-applications')->group(function () {
            Route::get('job-applications', [JobApplicationController::class, 'index']);
            Route::get('job-applications/statistics', [JobApplicationController::class, 'statistics']);
            Route::get('job-applications/{jobApplication}', [JobApplicationController::class, 'show']);
            Route::put('job-applications/{jobApplication}', [JobApplicationController::class, 'update']);
            Route::delete('job-applications/{jobApplication}', [JobApplicationController::class, 'destroy']);
        });

        // Upload routes - requires manage-media permission
        Route::middleware('permission:manage-media')->group(function () {
            Route::post('upload/image', [UploadController::class, 'uploadImage']);
            Route::post('upload/images', [UploadController::class, 'uploadMultipleImages']);
            Route::post('upload/file', [UploadController::class, 'uploadFile']);
            Route::delete('upload/image', [UploadController::class, 'deleteImage']);
        });

        // Leads - requires view-leads permission for viewing, manage-leads for updates/deletes
        Route::middleware('permission:view-leads')->group(function () {
            Route::get('leads', [LeadController::class, 'index']);
            Route::get('leads/statistics', [LeadController::class, 'statistics']);
            Route::get('leads/unread-count', [LeadController::class, 'unreadCount']);
            Route::get('leads/{lead}', [LeadController::class, 'show']);
            Route::post('leads', [LeadController::class, 'store']); // Creating leads (public submission)
            Route::post('leads/{lead}/mark-as-read', [LeadController::class, 'markAsRead']);
        });
        Route::middleware('permission:manage-leads')->group(function () {
            Route::put('leads/{lead}', [LeadController::class, 'update']);
            Route::delete('leads/{lead}', [LeadController::class, 'destroy']);
        });
        Route::middleware('permission:export-leads')->group(function () {
            Route::get('leads/export/csv', [LeadController::class, 'exportCsv']);
        });

        // Newsletter Subscriptions - requires manage-newsletters permission
        Route::middleware('permission:manage-newsletters')->group(function () {
            Route::get('newsletters', [NewsletterController::class, 'index']);
            Route::get('newsletters/{newsletterSubscription}', [NewsletterController::class, 'show']);
            Route::put('newsletters/{newsletterSubscription}', [NewsletterController::class, 'update']);
            Route::delete('newsletters/{newsletterSubscription}', [NewsletterController::class, 'destroy']);
            Route::get('newsletters/export/csv', [NewsletterController::class, 'exportCsv']);
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
