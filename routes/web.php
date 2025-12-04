<?php

use App\Http\Controllers\ArtisanController;
use Illuminate\Support\Facades\Route;

Route::post('/artisan/run', [ArtisanController::class, 'run']);

Route::get('/phpinfo', function () {
    ob_start();
    phpinfo();
    $content = ob_get_clean();

    return response($content)->header('Content-Type', 'text/html');
});

// Catch-all route for Vue.js SPA (must be last to not interfere with API routes)
Route::namespace('App\Http\Controllers')->group(function () {
    Route::get('{any?}', 'IndexController@index')->where('any', '^(?!api).*$');
});
