<?php

/**
 * PhpStorm Metadata for Laravel Alert
 * 
 * This file provides enhanced autocompletion and IntelliSense
 * support for PhpStorm IDE.
 * 
 * @package Wahyudedik\LaravelAlert
 * @version 1.0.0
 * @author Wahyudedik
 * @license MIT
 * @link https://github.com/wahyudedik/LaravelAlert
 */

namespace PHPSTORM_META {
    
    // Alert Facade
    override(\Wahyudedik\LaravelAlert\Facades\Alert::class, map([
        'success' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'error' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'warning' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'info' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'custom' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'ajax' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'websocket' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'pusher' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'email' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'bulk' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'clear' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'clearAll' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'count' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'has' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'get' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'all' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'render' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'renderAll' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
    ]));
    
    // Toast Facade
    override(\Wahyudedik\LaravelAlert\Facades\Toast::class, map([
        'success' => \Wahyudedik\LaravelAlert\Managers\ToastAlertManager::class,
        'error' => \Wahyudedik\LaravelAlert\Managers\ToastAlertManager::class,
        'warning' => \Wahyudedik\LaravelAlert\Managers\ToastAlertManager::class,
        'info' => \Wahyudedik\LaravelAlert\Managers\ToastAlertManager::class,
        'custom' => \Wahyudedik\LaravelAlert\Managers\ToastAlertManager::class,
    ]));
    
    // Modal Facade
    override(\Wahyudedik\LaravelAlert\Facades\Modal::class, map([
        'success' => \Wahyudedik\LaravelAlert\Managers\ModalAlertManager::class,
        'error' => \Wahyudedik\LaravelAlert\Managers\ModalAlertManager::class,
        'warning' => \Wahyudedik\LaravelAlert\Managers\ModalAlertManager::class,
        'info' => \Wahyudedik\LaravelAlert\Managers\ModalAlertManager::class,
        'custom' => \Wahyudedik\LaravelAlert\Managers\ModalAlertManager::class,
    ]));
    
    // Inline Facade
    override(\Wahyudedik\LaravelAlert\Facades\Inline::class, map([
        'success' => \Wahyudedik\LaravelAlert\Managers\InlineAlertManager::class,
        'error' => \Wahyudedik\LaravelAlert\Managers\InlineAlertManager::class,
        'warning' => \Wahyudedik\LaravelAlert\Managers\InlineAlertManager::class,
        'info' => \Wahyudedik\LaravelAlert\Managers\InlineAlertManager::class,
        'custom' => \Wahyudedik\LaravelAlert\Managers\InlineAlertManager::class,
    ]));
    
    // Service Container
    override(\Illuminate\Contracts\Container\Container::class, map([
        'alert.manager' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
        'alert.toast' => \Wahyudedik\LaravelAlert\Managers\ToastAlertManager::class,
        'alert.modal' => \Wahyudedik\LaravelAlert\Managers\ModalAlertManager::class,
        'alert.inline' => \Wahyudedik\LaravelAlert\Managers\InlineAlertManager::class,
        'alert.database' => \Wahyudedik\LaravelAlert\Managers\DatabaseAlertManager::class,
        'alert.redis' => \Wahyudedik\LaravelAlert\Managers\RedisAlertManager::class,
        'alert.cache' => \Wahyudedik\LaravelAlert\Managers\CacheAlertManager::class,
        'alert.pusher' => \Wahyudedik\LaravelAlert\Integrations\PusherIntegration::class,
        'alert.websocket' => \Wahyudedik\LaravelAlert\Integrations\WebSocketIntegration::class,
        'alert.email' => \Wahyudedik\LaravelAlert\Integrations\EmailIntegration::class,
        'alert.performance' => \Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class,
        'alert.animation' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
    ]));
    
    // Configuration
    override(\Illuminate\Contracts\Config\Repository::class, map([
        'laravel-alert' => \Wahyudedik\LaravelAlert\TypeHints\AlertConfig::class,
        'laravel-alert.theme' => 'string',
        'laravel-alert.position' => 'string',
        'laravel-alert.dismissible' => 'bool',
        'laravel-alert.auto_dismiss' => 'bool',
        'laravel-alert.auto_dismiss_delay' => 'int',
        'laravel-alert.session_key' => 'string',
        'laravel-alert.javascript_enabled' => 'bool',
        'laravel-alert.themes' => 'array',
        'laravel-alert.positions' => 'array',
        'laravel-alert.animations' => 'array',
        'laravel-alert.icons' => 'array',
        'laravel-alert.priorities' => 'array',
        'laravel-alert.contexts' => 'array',
        'laravel-alert.fields' => 'array',
        'laravel-alert.storage' => 'array',
        'laravel-alert.cache' => 'array',
        'laravel-alert.redis' => 'array',
        'laravel-alert.performance' => 'array',
        'laravel-alert.pusher' => 'array',
        'laravel-alert.websocket' => 'array',
        'laravel-alert.email' => 'array',
    ]));
    
    // Blade Components
    override(\Illuminate\View\View::class, map([
        'laravel-alert::components.alert' => \Wahyudedik\LaravelAlert\View\Components\AlertComponent::class,
        'laravel-alert::components.alerts' => \Wahyudedik\LaravelAlert\View\Components\AlertsComponent::class,
        'laravel-alert::components.toast' => \Wahyudedik\LaravelAlert\View\Components\ToastComponent::class,
        'laravel-alert::components.modal' => \Wahyudedik\LaravelAlert\View\Components\ModalComponent::class,
        'laravel-alert::components.inline' => \Wahyudedik\LaravelAlert\View\Components\InlineComponent::class,
    ]));
    
    // Middleware
    override(\Illuminate\Routing\Router::class, map([
        'alert' => \Wahyudedik\LaravelAlert\Http\Middleware\AlertMiddleware::class,
        'laravel-alert.api.auth' => \Wahyudedik\LaravelAlert\Http\Middleware\ApiAuthentication::class,
        'laravel-alert.admin.auth' => \Wahyudedik\LaravelAlert\Http\Middleware\AdminAuthentication::class,
        'laravel-alert.webhook.auth' => \Wahyudedik\LaravelAlert\Http\Middleware\WebhookAuthentication::class,
        'laravel-alert.cors' => \Wahyudedik\LaravelAlert\Http\Middleware\CorsMiddleware::class,
    ]));
    
    // Console Commands
    override(\Illuminate\Console\Kernel::class, map([
        'laravel-alert:install' => \Wahyudedik\LaravelAlert\Console\Commands\InstallCommand::class,
        'laravel-alert:publish' => \Wahyudedik\LaravelAlert\Console\Commands\PublishCommand::class,
        'laravel-alert:clear' => \Wahyudedik\LaravelAlert\Console\Commands\ClearCommand::class,
        'laravel-alert:status' => \Wahyudedik\LaravelAlert\Console\Commands\StatusCommand::class,
        'laravel-alert:test' => \Wahyudedik\LaravelAlert\Console\Commands\TestCommand::class,
    ]));
    
    // Routes
    override(\Illuminate\Routing\Route::class, map([
        'laravel-alert.api' => \Wahyudedik\LaravelAlert\Http\Controllers\Api\AlertApiController::class,
        'laravel-alert.admin' => \Wahyudedik\LaravelAlert\Http\Controllers\Admin\AlertAdminController::class,
        'laravel-alert.webhook' => \Wahyudedik\LaravelAlert\Http\Controllers\Webhook\AlertWebhookController::class,
    ]));
    
    // Models
    override(\Illuminate\Database\Eloquent\Model::class, map([
        'Alert' => \Wahyudedik\LaravelAlert\Models\Alert::class,
        'AlertSession' => \Wahyudedik\LaravelAlert\Models\AlertSession::class,
        'AlertStatistics' => \Wahyudedik\LaravelAlert\Models\AlertStatistics::class,
    ]));
    
    // Cache
    override(\Illuminate\Cache\CacheManager::class, map([
        'laravel-alert' => \Wahyudedik\LaravelAlert\Managers\CacheAlertManager::class,
    ]));
    
    // Redis
    override(\Illuminate\Redis\RedisManager::class, map([
        'laravel-alert' => \Wahyudedik\LaravelAlert\Managers\RedisAlertManager::class,
    ]));
    
    // Session
    override(\Illuminate\Session\SessionManager::class, map([
        'laravel-alert' => \Wahyudedik\LaravelAlert\Managers\AlertManager::class,
    ]));
    
    // Mail
    override(\Illuminate\Mail\MailManager::class, map([
        'laravel-alert' => \Wahyudedik\LaravelAlert\Integrations\EmailIntegration::class,
    ]));
    
    // Queue
    override(\Illuminate\Queue\QueueManager::class, map([
        'laravel-alert' => \Wahyudedik\LaravelAlert\Integrations\EmailIntegration::class,
    ]));
    
    // Broadcasting
    override(\Illuminate\Broadcasting\BroadcastManager::class, map([
        'laravel-alert' => \Wahyudedik\LaravelAlert\Integrations\PusherIntegration::class,
    ]));
    
    // WebSocket
    override(\Illuminate\WebSocket\WebSocketManager::class, map([
        'laravel-alert' => \Wahyudedik\LaravelAlert\Integrations\WebSocketIntegration::class,
    ]));
    
    // Performance
    override(\Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class, map([
        'optimize' => \Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class,
        'batch' => \Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class,
        'lazy' => \Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class,
        'cache' => \Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class,
        'redis' => \Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class,
        'database' => \Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class,
        'session' => \Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class,
        'ajax' => \Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class,
        'websocket' => \Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class,
        'pusher' => \Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class,
        'email' => \Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer::class,
    ]));
    
    // Animation
    override(\Wahyudedik\LaravelAlert\Managers\AnimationManager::class, map([
        'fadeIn' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'fadeOut' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'slideIn' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'slideOut' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'bounceIn' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'bounceOut' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'zoomIn' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'zoomOut' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'flipIn' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'flipOut' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'rotateIn' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'rotateOut' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'pulse' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'shake' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'wobble' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'tada' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'jello' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'heartbeat' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'flash' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
        'glow' => \Wahyudedik\LaravelAlert\Managers\AnimationManager::class,
    ]));
}
