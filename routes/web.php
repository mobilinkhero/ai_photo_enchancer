<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PhotosController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ApiLogsController;
use App\Http\Controllers\Admin\FeaturesController;
use App\Http\Controllers\Admin\AiTestController;
use App\Http\Controllers\Api\AppApiController;

// ── Public App API (called by Flutter) ──────────────────────────
Route::prefix('api')->group(function () {
    Route::get('/config', [AppApiController::class, 'config']);
    Route::post('/enhance', [AppApiController::class, 'enhance'])->defaults('tool', 'enhance');
    Route::post('/restore', [AppApiController::class, 'enhance'])->defaults('tool', 'restore');
    Route::post('/face', [AppApiController::class, 'enhance'])->defaults('tool', 'face');
    Route::post('/colorize', [AppApiController::class, 'enhance'])->defaults('tool', 'colorize');
    Route::post('/upscale', [AppApiController::class, 'enhance'])->defaults('tool', 'upscale');
    Route::post('/background', [AppApiController::class, 'enhance'])->defaults('tool', 'background');
    // Generic fallback: /api/{tool}
    Route::post('/{tool}', [AppApiController::class, 'enhance']);
});

Route::get('/', fn() => redirect()->route('admin.login'));

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(\App\Http\Middleware\AdminAuth::class)->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Photos
        Route::prefix('photos')->name('photos.')->group(function () {
            Route::get('/', [PhotosController::class, 'index'])->name('index');
            Route::post('/{photo}/retry', [PhotosController::class, 'retry'])->name('retry');
            Route::delete('/{photo}', [PhotosController::class, 'destroy'])->name('destroy');
        });

        // Settings — 3 separate pages
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/general', [SettingsController::class, 'general'])->name('general');
            Route::post('/general', [SettingsController::class, 'updateGeneral'])->name('general.update');

            Route::get('/billing', [SettingsController::class, 'billing'])->name('billing');
            Route::post('/billing', [SettingsController::class, 'updateBilling'])->name('billing.update');

            Route::get('/ai', [SettingsController::class, 'aiSettings'])->name('ai');
            Route::post('/ai', [SettingsController::class, 'updateAi'])->name('ai.update');
        });

        // API Logs
        Route::prefix('api-logs')->name('api-logs.')->group(function () {
            Route::get('/', [ApiLogsController::class, 'index'])->name('index');
            Route::get('/{apiLog}', [ApiLogsController::class, 'show'])->name('show');
            Route::delete('/{apiLog}', [ApiLogsController::class, 'destroy'])->name('destroy');
            Route::post('/clear', [ApiLogsController::class, 'clear'])->name('clear');
        });

        // App Features (controls what shows in Flutter app)
        Route::prefix('features')->name('features.')->group(function () {
            Route::get('/', [FeaturesController::class, 'index'])->name('index');
            Route::put('/{feature}', [FeaturesController::class, 'update'])->name('update');
            Route::post('/{feature}/toggle', [FeaturesController::class, 'toggleEnabled'])->name('toggle');
        });

        // Settings > Ads (AdMob)
        Route::get('/settings/ads', [SettingsController::class, 'ads'])->name('settings.ads');
        Route::post('/settings/ads', [SettingsController::class, 'updateAds'])->name('settings.ads.update');

        // AI Test Lab
        Route::get('/ai-test', [AiTestController::class, 'index'])->name('ai-test.index');
        Route::post('/ai-test/process', [AiTestController::class, 'process'])->name('ai-test.process');
    });
});
