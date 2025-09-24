<?php

/**
 * Laravel Alert Usage Examples
 * 
 * This file demonstrates various ways to use the Laravel Alert library.
 */

use Wahyudedik\LaravelAlert\Facades\Alert;

// ===========================================
// BASIC USAGE WITH FACADE
// ===========================================

// Simple alerts
Alert::success('Operation completed successfully!');
Alert::error('Something went wrong!');
Alert::warning('Please check your input.');
Alert::info('Welcome to our application!');

// Alerts with titles
Alert::success('User created successfully!', 'Success');
Alert::error('Failed to save data', 'Error');

// Custom alerts
Alert::add('custom', 'This is a custom alert!');

// ===========================================
// ADVANCED USAGE
// ===========================================

// Alerts with custom options
Alert::success('Data saved!', 'Success', [
    'dismissible' => true,
    'icon' => 'fas fa-check',
    'class' => 'custom-success-class',
    'style' => 'border-left: 4px solid #28a745;'
]);

// Non-dismissible alert
Alert::error('Critical error occurred!', 'Critical', [
    'dismissible' => false,
    'icon' => 'fas fa-exclamation-triangle'
]);

// ===========================================
// BULK OPERATIONS
// ===========================================

// Add multiple alerts at once
Alert::addMultiple([
    ['type' => 'success', 'message' => 'User created successfully'],
    ['type' => 'info', 'message' => 'Email sent to user'],
    ['type' => 'warning', 'message' => 'Please verify email address']
]);

// ===========================================
// ALERT MANAGEMENT
// ===========================================

// Check if there are alerts
if (Alert::hasAlerts()) {
    echo "There are " . Alert::count() . " alerts";
}

// Get alerts by type
$successAlerts = Alert::getAlertsByType('success');
$errorAlerts = Alert::getAlertsByType('error');

// Get first and last alerts
$firstAlert = Alert::first();
$lastAlert = Alert::last();

// Clear specific alert types
Alert::clearByType('info'); // Clear only info alerts
Alert::clear(); // Clear all alerts

// Flush alerts (get them and clear)
$allAlerts = Alert::flush();

// ===========================================
// BLADE COMPONENT USAGE
// ===========================================

/*
In your Blade templates:

<!-- Single alert component -->
<x-alert type="success" dismissible>
    Your changes have been saved!
</x-alert>

<!-- Alert with title and custom options -->
<x-alert 
    type="error" 
    title="Critical Error"
    :options="['icon' => 'fas fa-exclamation-triangle', 'dismissible' => false]"
>
    Something went wrong!
</x-alert>

<!-- Display all alerts -->
<x-alerts />

<!-- Using Blade directives -->
@alert('info', 'Welcome to our application!')
@alerts
@alertIf($user->isNew(), 'success', 'Welcome to our platform!')
*/

// ===========================================
// CONTROLLER USAGE EXAMPLES
// ===========================================

class UserController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = User::create($request->validated());

            Alert::success('User created successfully!', 'Success');

            return redirect()->route('users.index');
        } catch (Exception $e) {
            Alert::error('Failed to create user: ' . $e->getMessage(), 'Error');

            return back()->withInput();
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $user->update($request->validated());

            Alert::success('User updated successfully!');

            return redirect()->route('users.show', $user);
        } catch (Exception $e) {
            Alert::error('Failed to update user: ' . $e->getMessage());

            return back()->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();

            Alert::success('User deleted successfully!');

            return redirect()->route('users.index');
        } catch (Exception $e) {
            Alert::error('Failed to delete user: ' . $e->getMessage());

            return back();
        }
    }
}

// ===========================================
// MIDDLEWARE USAGE
// ===========================================

class AlertMiddleware
{
    public function handle($request, Closure $next)
    {
        // Add system alerts
        if (config('app.debug')) {
            Alert::info('Debug mode is enabled', 'Debug');
        }

        // Add maintenance alerts
        if (app()->isDownForMaintenance()) {
            Alert::warning('Application is in maintenance mode', 'Maintenance');
        }

        return $next($request);
    }
}

// ===========================================
// API RESPONSE USAGE
// ===========================================

class ApiController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->validated();
            $model = Model::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Data created successfully',
                'data' => $model,
                'alerts' => Alert::getAlerts() // Include alerts in API response
            ]);
        } catch (Exception $e) {
            Alert::error('Failed to create data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create data',
                'alerts' => Alert::getAlerts()
            ], 400);
        }
    }
}

// ===========================================
// CONFIGURATION EXAMPLES
// ===========================================

/*
// config/laravel-alert.php
return [
    'default_theme' => 'bootstrap', // or 'tailwind', 'bulma'
    'auto_dismiss' => true,
    'dismiss_delay' => 5000, // 5 seconds
    'animation' => 'fade',
    'position' => 'top-right',
    'max_alerts' => 5,
    'session_key' => 'laravel_alerts',
];
*/

// ===========================================
// CUSTOM THEMES
// ===========================================

/*
You can create custom themes by publishing the views:

php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="laravel-alert-views"

Then customize the templates in resources/views/vendor/laravel-alert/
*/
