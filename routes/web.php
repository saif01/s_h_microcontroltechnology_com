<?php

use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PageController as PublicPageController;
use App\Http\Controllers\Public\ProductController as PublicProductController;
use App\Http\Controllers\Public\ServiceController as PublicServiceController;
use App\Http\Controllers\Api\SettingController as ApiSettingController;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::prefix('api/public')->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/pages/{slug}', [PublicPageController::class, 'show']);
    Route::get('/services', [PublicServiceController::class, 'index']);
    Route::get('/services/{slug}', [PublicServiceController::class, 'show']);
    Route::get('/products', [PublicProductController::class, 'index']);
    Route::get('/products/{slug}', [PublicProductController::class, 'show']);
    Route::get('/settings', [ApiSettingController::class, 'publicIndex']);
    Route::post('/contact', [ContactController::class, 'submit']);
});

// Catch-all route for Vue.js SPA (must be last to not interfere with API routes)
Route::namespace('App\Http\Controllers')->group(function () {
    Route::get('{any?}', 'IndexController@index')->where('any', '^(?!api).*$');
});
