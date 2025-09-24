<?php

/**
 * Enhanced Laravel Alert Usage Examples
 * 
 * This file demonstrates advanced features of the Laravel Alert library including
 * expiration, auto-dismiss, custom styling, and more.
 */

use Wahyudedik\LaravelAlert\Facades\Alert;

// ===========================================
// EXPIRATION AND AUTO-DISMISS FEATURES
// ===========================================

// Temporary alert (expires in 5 minutes)
Alert::temporary('info', 'This alert will expire in 5 minutes', 'Temporary Alert', 300);

// Flash alert (auto-dismisses after 3 seconds)
Alert::flash('success', 'Operation completed!', 'Success', 3000);

// Alert with custom expiration (1 hour)
Alert::addWithExpiration('warning', 'This will expire in 1 hour', 'Warning', 3600);

// Alert with custom auto-dismiss (10 seconds)
Alert::addWithAutoDismiss('info', 'This will auto-dismiss in 10 seconds', 'Info', 10000);

// ===========================================
// ADVANCED STYLING AND CUSTOMIZATION
// ===========================================

// Alert with custom CSS classes and styles
Alert::success('Custom styled alert!', 'Styled', [
    'class' => 'custom-success-alert my-custom-class',
    'style' => 'border-left: 4px solid #28a745; background: linear-gradient(45deg, #f8f9fa, #e9ecef);',
    'icon' => 'fas fa-check-circle',
    'animation' => 'slide',
    'theme' => 'custom'
]);

// Alert with data attributes
Alert::error('Error with data attributes', 'Error', [
    'data_attributes' => [
        'tracking' => 'error-123',
        'category' => 'validation',
        'priority' => 'high'
    ],
    'class' => 'trackable-error'
]);

// Alert with HTML content
Alert::info('Alert with <strong>HTML content</strong> and <em>formatting</em>', 'HTML Alert', [
    'html_content' => '<div class="alert-content"><strong>Bold text</strong> and <em>italic text</em></div>',
    'class' => 'html-alert'
]);

// ===========================================
// POSITIONING AND THEMING
// ===========================================

// Alert with specific position
Alert::warning('This alert will appear in bottom-left', 'Positioned Alert', [
    'position' => 'bottom-left',
    'animation' => 'bounce'
]);

// Alert with custom theme
Alert::info('Custom themed alert', 'Themed Alert', [
    'theme' => 'dark',
    'class' => 'dark-theme-alert'
]);

// ===========================================
// CONTROLLER USAGE WITH EXPIRATION
// ===========================================

class EnhancedUserController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = User::create($request->validated());

            // Success alert that expires in 10 minutes
            Alert::addWithExpiration(
                'success',
                'User created successfully!',
                'Success',
                600, // 10 minutes
                ['icon' => 'fas fa-user-plus']
            );

            return redirect()->route('users.index');
        } catch (Exception $e) {
            // Error alert that auto-dismisses after 5 seconds
            Alert::flash(
                'error',
                'Failed to create user: ' . $e->getMessage(),
                'Error',
                5000,
                ['dismissible' => false] // Critical error, not dismissible
            );

            return back()->withInput();
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $user->update($request->validated());

            // Flash success with custom styling
            Alert::flash('success', 'User updated successfully!', 'Updated', 3000, [
                'class' => 'update-success',
                'style' => 'border-left: 4px solid #28a745;',
                'icon' => 'fas fa-edit',
                'animation' => 'fade'
            ]);

            return redirect()->route('users.show', $user);
        } catch (Exception $e) {
            Alert::error('Failed to update user: ' . $e->getMessage(), 'Update Failed', [
                'data_attributes' => ['user_id' => $user->id, 'action' => 'update'],
                'class' => 'update-error'
            ]);

            return back()->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();

            // Temporary success alert (expires in 2 minutes)
            Alert::temporary('success', 'User deleted successfully!', 'Deleted', 120, [
                'icon' => 'fas fa-trash',
                'class' => 'delete-success'
            ]);

            return redirect()->route('users.index');
        } catch (Exception $e) {
            Alert::error('Failed to delete user: ' . $e->getMessage(), 'Delete Failed', [
                'dismissible' => false, // Critical error
                'class' => 'delete-error'
            ]);

            return back();
        }
    }
}

// ===========================================
// MIDDLEWARE WITH EXPIRATION
// ===========================================

class EnhancedAlertMiddleware
{
    public function handle($request, Closure $next)
    {
        // Debug alert that expires in 1 hour
        if (config('app.debug')) {
            Alert::addWithExpiration(
                'info',
                'Debug mode is enabled',
                'Debug',
                3600,
                ['class' => 'debug-alert', 'icon' => 'fas fa-bug']
            );
        }

        // Maintenance alert that doesn't expire
        if (app()->isDownForMaintenance()) {
            Alert::warning('Application is in maintenance mode', 'Maintenance', [
                'dismissible' => false,
                'class' => 'maintenance-alert',
                'icon' => 'fas fa-tools'
            ]);
        }

        // Session timeout warning (expires in 30 minutes)
        if (session('last_activity') && (time() - session('last_activity')) > 1800) {
            Alert::temporary(
                'warning',
                'Your session will expire soon. Please save your work.',
                'Session Warning',
                1800, // 30 minutes
                ['class' => 'session-warning']
            );
        }

        return $next($request);
    }
}

// ===========================================
// API RESPONSE WITH EXPIRATION
// ===========================================

class EnhancedApiController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->validated();
            $model = Model::create($data);

            // Add success alert with expiration
            Alert::addWithExpiration('success', 'Data created successfully', 'Success', 3600);

            return response()->json([
                'success' => true,
                'message' => 'Data created successfully',
                'data' => $model,
                'alerts' => Alert::getAlerts(),
                'expired_alerts' => Alert::getExpiredAlerts(),
                'auto_dismiss_alerts' => Alert::getAutoDismissAlerts()
            ]);
        } catch (Exception $e) {
            Alert::flash('error', 'Failed to create data: ' . $e->getMessage(), 'Error', 5000);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create data',
                'alerts' => Alert::getAlerts()
            ], 400);
        }
    }

    public function cleanup()
    {
        // Clean up expired alerts
        Alert::cleanupExpired();

        return response()->json([
            'message' => 'Expired alerts cleaned up',
            'remaining_alerts' => Alert::count()
        ]);
    }
}

// ===========================================
// BLADE COMPONENT USAGE WITH ENHANCED FEATURES
// ===========================================

/*
In your Blade templates:

<!-- Alert with expiration -->
<x-alert 
    type="info" 
    title="Temporary Alert"
    :options="[
        'expires_at' => time() + 300,
        'class' => 'temporary-alert',
        'icon' => 'fas fa-clock'
    ]"
>
    This alert will expire in 5 minutes
</x-alert>

<!-- Alert with auto-dismiss -->
<x-alert 
    type="success" 
    title="Flash Alert"
    :options="[
        'auto_dismiss_delay' => 3000,
        'animation' => 'fade',
        'class' => 'flash-alert'
    ]"
>
    This will auto-dismiss in 3 seconds
</x-alert>

<!-- Alert with custom styling -->
<x-alert 
    type="warning" 
    title="Styled Alert"
    :options="[
        'class' => 'custom-warning',
        'style' => 'border-left: 4px solid #ffc107;',
        'icon' => 'fas fa-exclamation-triangle',
        'data_attributes' => ['tracking' => 'warning-123']
    ]"
>
    Custom styled warning alert
</x-alert>

<!-- Alert with HTML content -->
<x-alert 
    type="info" 
    title="HTML Alert"
    :options="[
        'html_content' => '<div class=\"alert-content\"><strong>Bold</strong> and <em>italic</em> text</div>',
        'class' => 'html-alert'
    ]"
>
    Alert with HTML content
</x-alert>

<!-- Display all alerts with enhanced features -->
<x-alerts />
*/

// ===========================================
// CONFIGURATION EXAMPLES
// ===========================================

/*
// config/laravel-alert.php
return [
    'default_theme' => 'bootstrap',
    'auto_dismiss' => true,
    'dismiss_delay' => 5000,
    'animation' => 'fade',
    'position' => 'top-right',
    'max_alerts' => 5,
    'session_key' => 'laravel_alerts',
    
    // Enhanced configuration
    'default_expiration' => 3600, // 1 hour
    'default_auto_dismiss' => 5000, // 5 seconds
    'cleanup_expired' => true, // Auto-cleanup expired alerts
    'themes' => [
        'bootstrap' => [...],
        'tailwind' => [...],
        'custom' => [
            'alert_class' => 'custom-alert',
            'types' => [
                'success' => 'custom-success',
                'error' => 'custom-error',
                'warning' => 'custom-warning',
                'info' => 'custom-info',
            ],
        ],
    ],
];
*/

// ===========================================
// ADVANCED USAGE PATTERNS
// ===========================================

// Batch operations with different expiration times
$alerts = [
    ['type' => 'success', 'message' => 'User created', 'options' => ['expires_at' => time() + 300]],
    ['type' => 'info', 'message' => 'Email sent', 'options' => ['auto_dismiss_delay' => 2000]],
    ['type' => 'warning', 'message' => 'Please verify email', 'options' => ['expires_at' => time() + 3600]]
];

Alert::addMultiple($alerts);

// Conditional alerts with expiration
if ($user->isNew()) {
    Alert::temporary('success', 'Welcome to our platform!', 'Welcome', 600);
} elseif ($user->isReturning()) {
    Alert::flash('info', 'Welcome back!', 'Welcome Back', 2000);
}

// System alerts with different expiration strategies
Alert::addWithExpiration('info', 'System maintenance scheduled', 'Maintenance', 86400); // 24 hours
Alert::flash('success', 'Settings saved', 'Saved', 2000); // 2 seconds
Alert::temporary('warning', 'Please update your profile', 'Update Required', 1800); // 30 minutes
