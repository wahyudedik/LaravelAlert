# Installation Guide

This guide will help you install and configure the Laravel Alert library in your Laravel application.

## 📋 Prerequisites

Before installing the Laravel Alert library, make sure you have:

- **PHP**: 8.1 or higher
- **Laravel**: 9.0 or higher
- **Composer**: Latest version
- **Database**: MySQL, PostgreSQL, SQLite, or SQL Server

## 🚀 Installation

### Step 1: Install via Composer

```bash
composer require wahyudedik/laravel-alert
```

### Step 2: Publish Configuration

```bash
php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="laravel-alert-config"
```

### Step 3: Run Installation Command

```bash
php artisan alert:install
```

This command will:
- Publish configuration files
- Publish views
- Publish assets
- Set up database migrations (if needed)

### Step 4: Run Migrations (Optional)

If you want to use database storage for alerts:

```bash
php artisan migrate
```

## ⚙️ Configuration

### Basic Configuration

The configuration file will be published to `config/laravel-alert.php`. Here's the basic setup:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    */
    'default_theme' => env('LARAVEL_ALERT_THEME', 'bootstrap'),

    /*
    |--------------------------------------------------------------------------
    | Auto Dismiss
    |--------------------------------------------------------------------------
    */
    'auto_dismiss' => env('LARAVEL_ALERT_AUTO_DISMISS', true),

    /*
    |--------------------------------------------------------------------------
    | Dismiss Delay
    |--------------------------------------------------------------------------
    */
    'dismiss_delay' => env('LARAVEL_ALERT_DISMISS_DELAY', 5000),

    /*
    |--------------------------------------------------------------------------
    | Animation
    |--------------------------------------------------------------------------
    */
    'animation' => env('LARAVEL_ALERT_ANIMATION', 'fade'),

    /*
    |--------------------------------------------------------------------------
    | Position
    |--------------------------------------------------------------------------
    */
    'position' => env('LARAVEL_ALERT_POSITION', 'top-right'),

    /*
    |--------------------------------------------------------------------------
    | Max Alerts
    |--------------------------------------------------------------------------
    */
    'max_alerts' => env('LARAVEL_ALERT_MAX_ALERTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Session Key
    |--------------------------------------------------------------------------
    */
    'session_key' => env('LARAVEL_ALERT_SESSION_KEY', 'laravel_alerts'),
];
```

### Environment Variables

Add these variables to your `.env` file:

```env
# Laravel Alert Configuration
LARAVEL_ALERT_THEME=bootstrap
LARAVEL_ALERT_AUTO_DISMISS=true
LARAVEL_ALERT_DISMISS_DELAY=5000
LARAVEL_ALERT_ANIMATION=fade
LARAVEL_ALERT_POSITION=top-right
LARAVEL_ALERT_MAX_ALERTS=5
LARAVEL_ALERT_SESSION_KEY=laravel_alerts

# Storage Configuration
LARAVEL_ALERT_STORAGE_DRIVER=database
LARAVEL_ALERT_STORAGE_FALLBACK=session

# Cache Configuration
LARAVEL_ALERT_CACHE_ENABLED=true
LARAVEL_ALERT_CACHE_DRIVER=file
LARAVEL_ALERT_CACHE_TTL=3600

# Redis Configuration
LARAVEL_ALERT_REDIS_ENABLED=false
LARAVEL_ALERT_REDIS_CONNECTION=default
LARAVEL_ALERT_REDIS_TTL=3600

# API Configuration
LARAVEL_ALERT_API_ENABLED=true
LARAVEL_ALERT_API_AUTH_METHOD=token
LARAVEL_ALERT_API_RATE_LIMITING=true
```

## 🎨 Publishing Assets

### Publish All Resources

```bash
php artisan alert:publish
```

### Publish Specific Resources

```bash
# Publish configuration only
php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="laravel-alert-config"

# Publish views only
php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="laravel-alert-views"

# Publish assets only
php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="laravel-alert-assets"
```

## 🗄️ Database Setup

### Option 1: Database Storage

If you want to store alerts in the database:

```bash
# Run migrations
php artisan migrate

# The alerts table will be created with the following structure:
# - id (primary key)
# - type (alert type)
# - message (alert message)
# - title (alert title)
# - user_id (user who created the alert)
# - session_id (session ID)
# - alert_type (alert type: alert, toast, modal, inline)
# - theme (theme: bootstrap, tailwind, bulma)
# - position (position: top-right, top-left, etc.)
# - animation (animation type)
# - dismissible (boolean)
# - auto_dismiss (boolean)
# - auto_dismiss_delay (integer)
# - expires_at (timestamp)
# - priority (integer)
# - context (string)
# - field (string)
# - form (string)
# - icon (string)
# - class (string)
# - style (string)
# - html_content (text)
# - data_attributes (json)
# - options (json)
# - created_at (timestamp)
# - updated_at (timestamp)
# - is_active (boolean)
# - dismissed_at (timestamp)
# - read_at (timestamp)
```

### Option 2: Session Storage (Default)

Alerts are stored in the session by default. No database setup required.

## 🔧 Service Provider Registration

The service provider is automatically registered. If you need to register it manually:

```php
// config/app.php
'providers' => [
    // ...
    Wahyudedik\LaravelAlert\AlertServiceProvider::class,
],
```

## 🎯 Facade Registration

The Alert facade is automatically registered. If you need to register it manually:

```php
// config/app.php
'aliases' => [
    // ...
    'Alert' => Wahyudedik\LaravelAlert\Facades\Alert::class,
],
```

## 🧪 Testing Installation

### Test Basic Functionality

```php
// In your controller or route
use Wahyudedik\LaravelAlert\Facades\Alert;

Route::get('/test-alert', function () {
    Alert::success('Installation successful!');
    return view('welcome');
});
```

### Test Blade Components

```blade
{{-- In your Blade template --}}
<x-alert type="success" message="Installation successful!" />
<x-alerts />
```

### Test API Endpoints

```bash
# Test API health
curl -X GET http://your-app.com/api/v1/alerts/health

# Test API authentication
curl -X GET http://your-app.com/api/v1/alerts \
  -H "Authorization: Bearer your-token"
```

## 🚨 Troubleshooting

### Common Issues

#### 1. Service Provider Not Found

```bash
# Clear config cache
php artisan config:clear
php artisan cache:clear
```

#### 2. Views Not Found

```bash
# Publish views
php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="laravel-alert-views"
```

#### 3. Assets Not Loading

```bash
# Publish assets
php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="laravel-alert-assets"

# Clear cache
php artisan cache:clear
```

#### 4. Database Connection Issues

```bash
# Check database connection
php artisan migrate:status

# Run migrations
php artisan migrate
```

### Debug Mode

Enable debug mode in your `.env` file:

```env
APP_DEBUG=true
LARAVEL_ALERT_DEBUG=true
```

## 📚 Next Steps

After successful installation:

1. [Read the Quick Start Guide](quick-start.md)
2. [Explore Basic Usage](basic-usage.md)
3. [Learn about Blade Integration](blade-integration.md)
4. [Check out Advanced Features](advanced-alert-types.md)

## 🆘 Support

If you encounter any issues during installation:

- [GitHub Issues](https://github.com/wahyudedik/LaravelAlert/issues)
- [Documentation](https://github.com/wahyudedik/LaravelAlert/docs)
- [Discussions](https://github.com/wahyudedik/LaravelAlert/discussions)

---

**Next**: [Quick Start Guide](quick-start.md)
