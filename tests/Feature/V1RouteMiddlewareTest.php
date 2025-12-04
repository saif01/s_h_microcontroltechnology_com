<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

dataset('v1-protected-routes', function () {
    return [
        'dashboard' => ['GET', 'api/v1/dashboard', ['auth:sanctum', 'admin']],
        'about-index' => ['GET', 'api/v1/about', ['auth:sanctum', 'admin', 'permission:manage-about']],
        'services-index' => ['GET', 'api/v1/services', ['auth:sanctum', 'admin', 'permission:manage-services']],
        'products-reviews-all' => ['GET', 'api/v1/products/reviews/all', ['auth:sanctum', 'admin', 'permission:manage-products']],
        'products-index' => ['GET', 'api/v1/products', ['auth:sanctum', 'admin', 'permission:manage-products']],
        'blog-posts-index' => ['GET', 'api/v1/blog-posts', ['auth:sanctum', 'admin', 'permission:manage-blog']],
        'announcements-index' => ['GET', 'api/v1/announcements', ['auth:sanctum', 'admin', 'permission:manage-announcements']],
        'announcements-toggle' => ['PUT', 'api/v1/announcements/1/toggle-status', ['auth:sanctum', 'admin', 'permission:manage-announcements']],
        'careers-index' => ['GET', 'api/v1/careers', ['auth:sanctum', 'admin', 'permission:manage-careers']],
        'job-applications-index' => ['GET', 'api/v1/job-applications', ['auth:sanctum', 'admin', 'permission:manage-applications']],
        'job-applications-statistics' => ['GET', 'api/v1/job-applications/statistics', ['auth:sanctum', 'admin', 'permission:manage-applications']],
        'upload-image' => ['POST', 'api/v1/upload/image', ['auth:sanctum', 'admin', 'permission:manage-media']],
        'leads-index' => ['GET', 'api/v1/leads', ['auth:sanctum', 'admin', 'permission:view-leads']],
        'leads-update' => ['PUT', 'api/v1/leads/1', ['auth:sanctum', 'admin', 'permission:manage-leads']],
        'leads-export' => ['GET', 'api/v1/leads/export/csv', ['auth:sanctum', 'admin', 'permission:export-leads']],
        'newsletters-index' => ['GET', 'api/v1/newsletters', ['auth:sanctum', 'admin', 'permission:manage-newsletters']],
        'settings-index' => ['GET', 'api/v1/settings', ['auth:sanctum', 'admin', 'permission:manage-settings']],
        'users-index' => ['GET', 'api/v1/users', ['auth:sanctum', 'admin', 'permission:manage-users']],
        'roles-index' => ['GET', 'api/v1/roles', ['auth:sanctum', 'admin', 'permission:manage-roles']],
        'roles-permissions-sync' => ['PUT', 'api/v1/roles/1/permissions', ['auth:sanctum', 'admin', 'permission:manage-roles']],
        'permissions-groups' => ['GET', 'api/v1/permissions/groups', ['auth:sanctum', 'admin', 'permission:manage-roles']],
        'permissions-index' => ['GET', 'api/v1/permissions', ['auth:sanctum', 'admin', 'permission:manage-roles']],
        'login-logs-index' => ['GET', 'api/v1/login-logs', ['auth:sanctum', 'admin', 'permission:view-login-logs']],
        'login-logs-statistics' => ['GET', 'api/v1/login-logs/statistics', ['auth:sanctum', 'admin', 'permission:view-login-logs']],
        'visitor-logs-index' => ['GET', 'api/v1/visitor-logs', ['auth:sanctum', 'admin', 'permission:view-visitor-logs']],
        'visitor-logs-statistics' => ['GET', 'api/v1/visitor-logs/statistics', ['auth:sanctum', 'admin', 'permission:view-visitor-logs']],
        'visitor-logs-delete-multiple' => ['POST', 'api/v1/visitor-logs/delete-multiple', ['auth:sanctum', 'admin', 'permission:view-visitor-logs']],
    ];
});

it('enforces the expected middleware for v1 admin routes', function (string $method, string $uri, array $expectedMiddleware) {
    $route = Route::getRoutes()->match(Request::create($uri, $method));
    $middlewares = $route->gatherMiddleware();

    foreach ($expectedMiddleware as $middleware) {
        expect($middlewares)
            ->withMessage("{$method} {$uri} is missing {$middleware}. Registered middleware: " . implode(', ', $middlewares))
            ->toContain($middleware);
    }
})->with('v1-protected-routes');

