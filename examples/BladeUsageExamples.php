<?php

/**
 * Laravel Alert Blade Integration Examples
 * 
 * This file demonstrates how to use the Laravel Alert library with Blade components,
 * directives, and templates.
 */

use Wahyudedik\LaravelAlert\Facades\Alert;

// ===========================================
// BLADE COMPONENT USAGE
// ===========================================

/*
<!-- Basic Alert Component -->
<x-alert type="success" dismissible>
    Your changes have been saved!
</x-alert>

<!-- Alert with Title -->
<x-alert type="error" title="Critical Error" dismissible>
    Something went wrong!
</x-alert>

<!-- Alert with Custom Options -->
<x-alert 
    type="warning" 
    title="Warning"
    :options="[
        'icon' => 'fas fa-exclamation-triangle',
        'class' => 'custom-warning',
        'style' => 'border-left: 4px solid #ffc107;',
        'dismissible' => true
    ]"
>
    Please check your input!
</x-alert>

<!-- Alert with Expiration -->
<x-alert 
    type="info" 
    title="Temporary Alert"
    :options="[
        'expires_at' => time() + 300,
        'class' => 'temporary-alert'
    ]"
>
    This alert will expire in 5 minutes
</x-alert>

<!-- Alert with Auto-Dismiss -->
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

<!-- Alert with HTML Content -->
<x-alert 
    type="info" 
    title="HTML Alert"
    :options="[
        'html_content' => '<div class="alert-content"><strong>Bold text</strong> and <em>italic text</em></div>',
        'class' => 'html-alert'
    ]"
>
    Alert with HTML content
</x-alert>

<!-- Alert with Data Attributes -->
<x-alert 
    type="error" 
    title="Tracked Alert"
    :options="[
        'data_attributes' => [
            'tracking' => 'error-123',
            'category' => 'validation',
            'priority' => 'high'
        ],
        'class' => 'tracked-error'
    ]"
>
    This alert is being tracked
</x-alert>
*/

// ===========================================
// ALERTS COMPONENT USAGE
// ===========================================

/*
<!-- Display All Alerts -->
<x-alerts />

<!-- Alerts with Custom Container -->
<x-alerts 
    theme="bootstrap"
    position="bottom-right"
    animation="slide"
    :auto-clear="true"
    :max-alerts="3"
    container-class="custom-alerts-container"
    container-style="background: #f8f9fa; border-radius: 8px;"
/>

<!-- Alerts with Different Themes -->
<x-alerts theme="tailwind" position="top-center" />
<x-alerts theme="bulma" position="bottom-left" />

<!-- Alerts without Auto-Clear -->
<x-alerts :auto-clear="false" />
*/

// ===========================================
// BLADE DIRECTIVES USAGE
// ===========================================

/*
<!-- Simple Alert Directive -->
@alert('success', 'Operation completed successfully!')

<!-- Alert Directive with Title -->
@alert('error', 'Something went wrong!', 'Error')

<!-- Conditional Alert Directive -->
@alertIf($user->isNew(), 'success', 'Welcome to our platform!')
@alertIf($errors->any(), 'error', 'Please fix the errors below.')

<!-- Display All Alerts Directive -->
@alerts

<!-- Alert Directive with Options -->
@alert('warning', 'Please check your input!', 'Warning', [
    'icon' => 'fas fa-exclamation-triangle',
    'class' => 'custom-warning',
    'dismissible' => true
])
*/

// ===========================================
// CONTROLLER USAGE WITH BLADE
// ===========================================

class BladeUserController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = User::create($request->validated());

            // Add success alert
            Alert::success('User created successfully!', 'Success', [
                'icon' => 'fas fa-user-plus',
                'class' => 'user-created-alert'
            ]);

            return redirect()->route('users.index');
        } catch (Exception $e) {
            Alert::error('Failed to create user: ' . $e->getMessage(), 'Error', [
                'dismissible' => false,
                'class' => 'user-creation-error'
            ]);

            return back()->withInput();
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $user->update($request->validated());

            // Flash success alert
            Alert::flash('success', 'User updated successfully!', 'Updated', 3000, [
                'class' => 'user-updated-alert',
                'animation' => 'slide'
            ]);

            return redirect()->route('users.show', $user);
        } catch (Exception $e) {
            Alert::error('Failed to update user: ' . $e->getMessage(), 'Update Failed', [
                'data_attributes' => ['user_id' => $user->id],
                'class' => 'user-update-error'
            ]);

            return back()->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();

            // Temporary success alert
            Alert::temporary('success', 'User deleted successfully!', 'Deleted', 120, [
                'icon' => 'fas fa-trash',
                'class' => 'user-deleted-alert'
            ]);

            return redirect()->route('users.index');
        } catch (Exception $e) {
            Alert::error('Failed to delete user: ' . $e->getMessage(), 'Delete Failed', [
                'dismissible' => false,
                'class' => 'user-delete-error'
            ]);

            return back();
        }
    }
}

// ===========================================
// BLADE TEMPLATE EXAMPLES
// ===========================================

/*
<!-- app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel App')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
    
    <!-- Display all alerts -->
    <x-alerts />
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!-- users/index.blade.php -->
@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="row">
    <div class="col-12">
        <h1>Users</h1>
        
        <!-- Display alerts for this page -->
        <x-alerts />
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-primary">View</a>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

<!-- users/create.blade.php -->
@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="row">
    <div class="col-12">
        <h1>Create User</h1>
        
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary">Create User</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
*/

// ===========================================
// CUSTOM THEME USAGE
// ===========================================

/*
<!-- Custom Theme Alert Component -->
<x-alert 
    type="success" 
    title="Custom Theme"
    theme="custom"
    :options="[
        'class' => 'custom-success-alert',
        'style' => 'background: linear-gradient(45deg, #28a745, #20c997); color: white;',
        'icon' => 'fas fa-check-circle',
        'animation' => 'bounce'
    ]"
>
    This alert uses a custom theme!
</x-alert>

<!-- Custom Theme Alerts Container -->
<x-alerts 
    theme="custom"
    position="top-center"
    animation="slide"
    container-class="custom-alerts-container"
    container-style="background: #f8f9fa; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
/>
*/

// ===========================================
// ADVANCED BLADE USAGE
// ===========================================

/*
<!-- Conditional Alerts -->
@if(session('success'))
    <x-alert type="success" title="Success">
        {{ session('success') }}
    </x-alert>
@endif

@if(session('error'))
    <x-alert type="error" title="Error" :dismissible="false">
        {{ session('error') }}
    </x-alert>
@endif

@if($errors->any())
    <x-alert type="error" title="Validation Errors" :dismissible="false">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif

<!-- Loop through Custom Alerts -->
@foreach($customAlerts as $alert)
    <x-alert 
        :type="$alert['type']" 
        :title="$alert['title']"
        :options="$alert['options']"
    >
        {{ $alert['message'] }}
    </x-alert>
@endforeach

<!-- Dynamic Alert with User Data -->
<x-alert 
    type="info" 
    title="Welcome {{ $user->name }}!"
    :options="[
        'icon' => 'fas fa-user',
        'class' => 'welcome-alert',
        'data_attributes' => [
            'user_id' => $user->id,
            'user_name' => $user->name
        ]
    ]"
>
    You have {{ $user->notifications->count() }} unread notifications.
</x-alert>
*/

// ===========================================
// JAVASCRIPT INTEGRATION
// ===========================================

/*
<!-- Custom JavaScript for Alert Management -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dismiss all alerts
    window.dismissAllAlerts = function() {
        if (window.LaravelAlert) {
            window.LaravelAlert.dismissAll();
        }
    };
    
    // Dismiss specific alert
    window.dismissAlert = function(alertId) {
        if (window.LaravelAlert) {
            window.LaravelAlert.dismiss(alertId);
        }
    };
    
    // Get alerts count
    window.getAlertsCount = function() {
        if (window.LaravelAlert) {
            return window.LaravelAlert.getAlertsCount();
        }
        return 0;
    };
    
    // Auto-dismiss expired alerts
    setInterval(function() {
        const alerts = document.querySelectorAll('[data-expires-at]');
        alerts.forEach(alert => {
            const expiresAt = parseInt(alert.dataset.expiresAt);
            const now = Math.floor(Date.now() / 1000);
            if (expiresAt <= now) {
                alert.remove();
            }
        });
    }, 60000); // Check every minute
});
</script>
*/
