<?php

namespace Wahyudedik\LaravelAlert\Middleware;

use Closure;
use Illuminate\Http\Request;
use Wahyudedik\LaravelAlert\Facades\Alert;

class AlertMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Add system alerts based on environment
        $this->addSystemAlerts($request);

        // Add maintenance alerts
        $this->addMaintenanceAlerts($request);

        // Add session timeout alerts
        $this->addSessionTimeoutAlerts($request);

        // Add debug alerts
        $this->addDebugAlerts($request);

        // Add user-specific alerts
        $this->addUserAlerts($request);

        $response = $next($request);

        // Clean up expired alerts after response
        $this->cleanupExpiredAlerts();

        return $response;
    }

    /**
     * Add system alerts based on environment.
     */
    protected function addSystemAlerts(Request $request): void
    {
        // Debug mode alert
        if (config('app.debug') && !$request->session()->has('laravel_alert_debug_shown')) {
            Alert::info('Debug mode is enabled', 'Debug', [
                'icon' => 'fas fa-bug',
                'class' => 'debug-alert',
                'expires_at' => time() + 3600, // 1 hour
                'data_attributes' => ['type' => 'system', 'category' => 'debug']
            ]);
            $request->session()->put('laravel_alert_debug_shown', true);
        }

        // Development environment alert
        if (config('app.env') === 'local' && !$request->session()->has('laravel_alert_env_shown')) {
            Alert::warning('You are in local development environment', 'Development', [
                'icon' => 'fas fa-code',
                'class' => 'env-alert',
                'expires_at' => time() + 1800, // 30 minutes
                'data_attributes' => ['type' => 'system', 'category' => 'environment']
            ]);
            $request->session()->put('laravel_alert_env_shown', true);
        }
    }

    /**
     * Add maintenance alerts.
     */
    protected function addMaintenanceAlerts(Request $request): void
    {
        if (app()->isDownForMaintenance()) {
            Alert::warning('Application is in maintenance mode', 'Maintenance', [
                'dismissible' => false,
                'icon' => 'fas fa-tools',
                'class' => 'maintenance-alert',
                'data_attributes' => ['type' => 'system', 'category' => 'maintenance']
            ]);
        }
    }

    /**
     * Add session timeout alerts.
     */
    protected function addSessionTimeoutAlerts(Request $request): void
    {
        $sessionLifetime = config('session.lifetime', 120);
        $lastActivity = $request->session()->get('last_activity', time());
        $timeSinceActivity = time() - $lastActivity;

        // Alert when session is about to expire (5 minutes before)
        if ($timeSinceActivity > ($sessionLifetime * 60 - 300)) {
            Alert::warning('Your session will expire soon. Please save your work.', 'Session Warning', [
                'icon' => 'fas fa-clock',
                'class' => 'session-warning-alert',
                'expires_at' => time() + 300, // 5 minutes
                'data_attributes' => ['type' => 'system', 'category' => 'session']
            ]);
        }

        // Update last activity
        $request->session()->put('last_activity', time());
    }

    /**
     * Add debug alerts.
     */
    protected function addDebugAlerts(Request $request): void
    {
        if (config('app.debug')) {
            // Memory usage alert
            $memoryUsage = memory_get_usage(true);
            $memoryLimit = ini_get('memory_limit');
            $memoryLimitBytes = $this->convertToBytes($memoryLimit);

            if ($memoryUsage > ($memoryLimitBytes * 0.8)) {
                Alert::error('High memory usage detected', 'Memory Warning', [
                    'icon' => 'fas fa-memory',
                    'class' => 'memory-alert',
                    'expires_at' => time() + 600, // 10 minutes
                    'data_attributes' => [
                        'type' => 'debug',
                        'category' => 'memory',
                        'usage' => $memoryUsage,
                        'limit' => $memoryLimitBytes
                    ]
                ]);
            }

            // Query count alert (if available)
            if (class_exists('\Illuminate\Database\Events\QueryExecuted')) {
                $queryCount = $request->session()->get('laravel_alert_query_count', 0);
                if ($queryCount > 50) {
                    Alert::warning("High query count detected: {$queryCount} queries", 'Query Warning', [
                        'icon' => 'fas fa-database',
                        'class' => 'query-alert',
                        'expires_at' => time() + 300, // 5 minutes
                        'data_attributes' => [
                            'type' => 'debug',
                            'category' => 'queries',
                            'count' => $queryCount
                        ]
                    ]);
                }
            }
        }
    }

    /**
     * Add user-specific alerts.
     */
    protected function addUserAlerts(Request $request): void
    {
        $user = $request->user();

        if ($user) {
            // New user welcome alert
            if ($user->created_at && $user->created_at->diffInDays(now()) < 1) {
                Alert::success('Welcome to our platform!', 'Welcome', [
                    'icon' => 'fas fa-handshake',
                    'class' => 'welcome-alert',
                    'expires_at' => time() + 3600, // 1 hour
                    'data_attributes' => ['type' => 'user', 'category' => 'welcome']
                ]);
            }

            // Email verification alert
            if (method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
                Alert::warning('Please verify your email address', 'Email Verification', [
                    'icon' => 'fas fa-envelope',
                    'class' => 'verification-alert',
                    'dismissible' => false,
                    'data_attributes' => ['type' => 'user', 'category' => 'verification']
                ]);
            }

            // Profile completion alert
            if (method_exists($user, 'profile_completed') && !$user->profile_completed) {
                Alert::info('Complete your profile to get started', 'Profile Setup', [
                    'icon' => 'fas fa-user-edit',
                    'class' => 'profile-alert',
                    'expires_at' => time() + 1800, // 30 minutes
                    'data_attributes' => ['type' => 'user', 'category' => 'profile']
                ]);
            }
        }
    }

    /**
     * Clean up expired alerts.
     */
    protected function cleanupExpiredAlerts(): void
    {
        Alert::cleanupExpired();
    }

    /**
     * Convert memory limit string to bytes.
     */
    protected function convertToBytes(string $memoryLimit): int
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
        $memoryLimit = (int) $memoryLimit;

        switch ($last) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }

        return $memoryLimit;
    }
}
