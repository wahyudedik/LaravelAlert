<?php

use Illuminate\Support\Facades\Route;
use Wahyudedik\LaravelAlert\Http\Controllers\Api\AlertApiController;

/*
|--------------------------------------------------------------------------
| Laravel Alert API Routes
|--------------------------------------------------------------------------
|
| Here are the API routes for the Laravel Alert package.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::group([
    'prefix' => 'api/v1/alerts',
    'middleware' => ['api', 'laravel-alert.api.auth'],
    'namespace' => 'Wahyudedik\LaravelAlert\Http\Controllers\Api'
], function () {

    // Basic CRUD operations
    Route::get('/', [AlertApiController::class, 'index'])->name('api.alerts.index');
    Route::post('/', [AlertApiController::class, 'store'])->name('api.alerts.store');
    Route::get('/{id}', [AlertApiController::class, 'show'])->name('api.alerts.show');
    Route::put('/{id}', [AlertApiController::class, 'update'])->name('api.alerts.update');
    Route::patch('/{id}', [AlertApiController::class, 'update'])->name('api.alerts.patch');
    Route::delete('/{id}', [AlertApiController::class, 'destroy'])->name('api.alerts.destroy');

    // Alert actions
    Route::post('/{id}/dismiss', [AlertApiController::class, 'dismiss'])->name('api.alerts.dismiss');
    Route::post('/dismiss-all', [AlertApiController::class, 'dismissAll'])->name('api.alerts.dismiss-all');

    // Filtering and searching
    Route::get('/type/{type}', [AlertApiController::class, 'getByType'])->name('api.alerts.by-type');
    Route::get('/stats/overview', [AlertApiController::class, 'stats'])->name('api.alerts.stats');
    Route::get('/history/audit', [AlertApiController::class, 'history'])->name('api.alerts.history');

    // Bulk operations
    Route::post('/bulk/create', [AlertApiController::class, 'bulkStore'])->name('api.alerts.bulk-store');
    Route::patch('/bulk/update', [AlertApiController::class, 'bulkUpdate'])->name('api.alerts.bulk-update');
});

// Public API routes (no authentication required)
Route::group([
    'prefix' => 'api/v1/alerts/public',
    'middleware' => ['api'],
    'namespace' => 'Wahyudedik\LaravelAlert\Http\Controllers\Api'
], function () {

    // Public alert endpoints
    Route::get('/types', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'types' => ['success', 'error', 'warning', 'info'],
                'themes' => ['bootstrap', 'tailwind', 'bulma'],
                'positions' => ['top-right', 'top-left', 'bottom-right', 'bottom-left', 'top-center', 'bottom-center'],
                'animations' => ['fade', 'slide', 'bounce', 'scale', 'zoom', 'flip', 'rotate', 'pulse', 'shake', 'wobble']
            ]
        ]);
    })->name('api.alerts.public.types');

    Route::get('/config', function () {
        return response()->json([
            'success' => true,
            'data' => config('laravel-alert', [])
        ]);
    })->name('api.alerts.public.config');
});

// Admin API routes (admin authentication required)
Route::group([
    'prefix' => 'api/v1/alerts/admin',
    'middleware' => ['api', 'laravel-alert.api.auth', 'laravel-alert.admin.auth'],
    'namespace' => 'Wahyudedik\LaravelAlert\Http\Controllers\Api'
], function () {

    // Admin-specific endpoints
    Route::get('/dashboard', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Admin dashboard endpoint',
                'timestamp' => now()->toISOString()
            ]
        ]);
    })->name('api.alerts.admin.dashboard');

    Route::get('/users/{userId}/alerts', [AlertApiController::class, 'getUserAlerts'])->name('api.alerts.admin.user-alerts');
    Route::delete('/users/{userId}/alerts', [AlertApiController::class, 'clearUserAlerts'])->name('api.alerts.admin.clear-user-alerts');
});

// Webhook routes
Route::group([
    'prefix' => 'api/v1/alerts/webhooks',
    'middleware' => ['api', 'laravel-alert.webhook.auth'],
    'namespace' => 'Wahyudedik\LaravelAlert\Http\Controllers\Api'
], function () {

    // Webhook endpoints
    Route::post('/create', [AlertApiController::class, 'webhookCreate'])->name('api.alerts.webhooks.create');
    Route::post('/update', [AlertApiController::class, 'webhookUpdate'])->name('api.alerts.webhooks.update');
    Route::post('/delete', [AlertApiController::class, 'webhookDelete'])->name('api.alerts.webhooks.delete');
});

// Rate-limited routes
Route::group([
    'prefix' => 'api/v1/alerts/rate-limited',
    'middleware' => ['api', 'laravel-alert.api.auth', 'throttle:60,1'],
    'namespace' => 'Wahyudedik\LaravelAlert\Http\Controllers\Api'
], function () {

    // Rate-limited endpoints
    Route::get('/frequent', [AlertApiController::class, 'getFrequentAlerts'])->name('api.alerts.rate-limited.frequent');
    Route::post('/batch', [AlertApiController::class, 'batchProcess'])->name('api.alerts.rate-limited.batch');
});

// CORS-enabled routes
Route::group([
    'prefix' => 'api/v1/alerts/cors',
    'middleware' => ['api', 'laravel-alert.api.auth', 'laravel-alert.cors'],
    'namespace' => 'Wahyudedik\LaravelAlert\Http\Controllers\Api'
], function () {

    // CORS-enabled endpoints
    Route::options('/{any}', function () {
        return response()->json([], 200);
    })->where('any', '.*');

    Route::get('/cross-origin', [AlertApiController::class, 'getCrossOriginAlerts'])->name('api.alerts.cors.cross-origin');
    Route::post('/cross-origin', [AlertApiController::class, 'createCrossOriginAlert'])->name('api.alerts.cors.create-cross-origin');
});

// Versioned API routes
Route::group([
    'prefix' => 'api/v2/alerts',
    'middleware' => ['api', 'laravel-alert.api.auth'],
    'namespace' => 'Wahyudedik\LaravelAlert\Http\Controllers\Api'
], function () {

    // V2 API endpoints
    Route::get('/', [AlertApiController::class, 'indexV2'])->name('api.v2.alerts.index');
    Route::post('/', [AlertApiController::class, 'storeV2'])->name('api.v2.alerts.store');
    Route::get('/{id}', [AlertApiController::class, 'showV2'])->name('api.v2.alerts.show');
    Route::put('/{id}', [AlertApiController::class, 'updateV2'])->name('api.v2.alerts.update');
    Route::delete('/{id}', [AlertApiController::class, 'destroyV2'])->name('api.v2.alerts.destroy');
});

// Health check routes
Route::get('/api/v1/alerts/health', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0',
            'uptime' => time() - $_SERVER['REQUEST_TIME_FLOAT']
        ]
    ]);
})->name('api.alerts.health');

// API documentation routes
Route::get('/api/v1/alerts/docs', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'title' => 'Laravel Alert API Documentation',
            'version' => '1.0.0',
            'description' => 'RESTful API for Laravel Alert package',
            'endpoints' => [
                'GET /api/v1/alerts' => 'List all alerts',
                'POST /api/v1/alerts' => 'Create new alert',
                'GET /api/v1/alerts/{id}' => 'Get specific alert',
                'PUT /api/v1/alerts/{id}' => 'Update alert',
                'DELETE /api/v1/alerts/{id}' => 'Delete alert',
                'POST /api/v1/alerts/{id}/dismiss' => 'Dismiss alert',
                'POST /api/v1/alerts/dismiss-all' => 'Dismiss all alerts',
                'GET /api/v1/alerts/type/{type}' => 'Get alerts by type',
                'GET /api/v1/alerts/stats/overview' => 'Get alert statistics',
                'GET /api/v1/alerts/history/audit' => 'Get alert history',
                'POST /api/v1/alerts/bulk/create' => 'Create multiple alerts',
                'PATCH /api/v1/alerts/bulk/update' => 'Update multiple alerts'
            ],
            'authentication' => [
                'type' => 'Bearer Token',
                'header' => 'Authorization: Bearer {token}',
                'alternative' => 'X-API-Key: {key}'
            ],
            'rate_limiting' => [
                'limit' => '60 requests per minute',
                'header' => 'X-RateLimit-Limit',
                'remaining' => 'X-RateLimit-Remaining',
                'reset' => 'X-RateLimit-Reset'
            ]
        ]
    ]);
})->name('api.alerts.docs');
