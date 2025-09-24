# Laravel Package Discovery

Laravel Alert is designed to work seamlessly with Laravel's package discovery system. This means that once you install the package, it will automatically register all its components without requiring manual configuration.

## 🚀 Auto-Discovery Features

### Service Provider Auto-Registration
The `AlertServiceProvider` is automatically registered when the package is installed:

```php
// Automatically registered
Wahyudedik\LaravelAlert\AlertServiceProvider
```

### Facade Auto-Registration
All facades are automatically registered and available:

```php
// Available without manual registration
use Wahyudedik\LaravelAlert\Facades\Alert;
use Wahyudedik\LaravelAlert\Facades\Toast;
use Wahyudedik\LaravelAlert\Facades\Modal;
use Wahyudedik\LaravelAlert\Facades\Inline;
```

### Console Commands Auto-Registration
Console commands are automatically registered:

```bash
# Available commands
php artisan laravel-alert:install
php artisan laravel-alert:publish
php artisan laravel-alert:clear
```

### Middleware Auto-Registration
Middleware is automatically registered:

```php
// Available middleware
'alert' => 'Wahyudedik\LaravelAlert\Http\Middleware\AlertMiddleware'
'laravel-alert.api.auth' => 'Wahyudedik\LaravelAlert\Http\Middleware\ApiAuthentication'
'laravel-alert.admin.auth' => 'Wahyudedik\LaravelAlert\Http\Middleware\AdminAuthentication'
'laravel-alert.webhook.auth' => 'Wahyudedik\LaravelAlert\Http\Middleware\WebhookAuthentication'
'laravel-alert.cors' => 'Wahyudedik\LaravelAlert\Http\Middleware\CorsMiddleware'
```

### Routes Auto-Registration
Routes are automatically registered:

```php
// API routes
Route::prefix('api/v1')->middleware(['api', 'laravel-alert.api.auth'])->group(function () {
    // Alert API routes
});

// Web routes
Route::prefix('admin')->middleware(['web', 'laravel-alert.admin.auth'])->group(function () {
    // Admin routes
});
```

### Views Auto-Registration
Blade components and views are automatically registered:

```php
// Available components
<x-alert type="success" message="Hello World!" />
<x-alerts />
<x-alert-toast type="info" message="Toast message!" />
<x-alert-modal type="warning" message="Modal message!" />
<x-alert-inline type="error" message="Inline message!" />
```

### Assets Auto-Registration
CSS and JavaScript assets are automatically registered:

```php
// Available assets
resources/css/laravel-alert.css
resources/css/themes/bootstrap.css
resources/css/themes/tailwind.css
resources/css/themes/bulma.css
resources/js/laravel-alert.js
resources/js/ajax.js
resources/js/websocket.js
resources/js/pusher.js
```

### Configuration Auto-Registration
Configuration files are automatically registered:

```php
// Available config
config('laravel-alert.theme')
config('laravel-alert.position')
config('laravel-alert.dismissible')
config('laravel-alert.auto_dismiss')
config('laravel-alert.auto_dismiss_delay')
```

### Database Auto-Registration
Migrations and seeders are automatically registered:

```php
// Available migrations
database/migrations/2024_01_01_000000_create_alerts_table.php

// Available seeders
database/seeders/AlertSeeder.php
```

### Translations Auto-Registration
Language files are automatically registered:

```php
// Available translations
resources/lang/en/laravel-alert.php
resources/lang/es/laravel-alert.php
resources/lang/fr/laravel-alert.php
resources/lang/de/laravel-alert.php
```

## 🔧 Manual Configuration

If you need to manually configure the package, you can do so by publishing the configuration file:

```bash
php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="config"
```

This will publish the configuration file to `config/laravel-alert.php` where you can customize the settings.

## 📦 Package Information

The package provides comprehensive information about itself:

### Package Details
- **Name**: `wahyudedik/laravel-alert`
- **Description**: A comprehensive Laravel alert system with multiple types, themes, and integrations
- **Version**: `1.0.0`
- **Author**: `Wahyudedik`
- **Email**: `wahyudedik@gmail.com`
- **Homepage**: `https://github.com/wahyudedik/LaravelAlert`
- **License**: `MIT`

### Support Information
- **Email**: `wahyudedik@gmail.com`
- **Issues**: `https://github.com/wahyudedik/LaravelAlert/issues`
- **Source**: `https://github.com/wahyudedik/LaravelAlert`
- **Documentation**: `https://wahyudedik.github.io/LaravelAlert`

### Funding Information
- **GitHub Sponsors**: `https://github.com/sponsors/wahyudedik`

### Keywords
The package is tagged with comprehensive keywords for easy discovery:

- `laravel` - Laravel framework
- `alert` - Alert system
- `notification` - Notification system
- `toast` - Toast notifications
- `modal` - Modal notifications
- `inline` - Inline notifications
- `blade` - Blade integration
- `javascript` - JavaScript integration
- `ajax` - AJAX support
- `websocket` - WebSocket support
- `pusher` - Pusher integration
- `email` - Email notifications
- `api` - REST API
- `rest` - RESTful API
- `real-time` - Real-time notifications
- `bootstrap` - Bootstrap theme
- `tailwind` - Tailwind CSS theme
- `bulma` - Bulma theme
- `responsive` - Responsive design
- `accessible` - Accessibility features
- `i18n` - Internationalization
- `customizable` - Customization options
- `performance` - Performance optimization
- `optimized` - Optimized for performance
- `testing` - Comprehensive testing
- `documentation` - Complete documentation

## 🧪 Testing Auto-Discovery

The package includes comprehensive tests to verify auto-discovery functionality:

```bash
# Run discovery tests
php artisan test tests/Discovery/PackageDiscoveryTest.php

# Run all tests
php artisan test
```

### Test Coverage
- ✅ Service Provider Auto-Registration
- ✅ Facade Auto-Registration
- ✅ Console Commands Auto-Registration
- ✅ Middleware Auto-Registration
- ✅ Routes Auto-Registration
- ✅ Views Auto-Registration
- ✅ Assets Auto-Registration
- ✅ Configuration Auto-Registration
- ✅ Database Auto-Registration
- ✅ Translations Auto-Registration
- ✅ Blade Components Auto-Registration
- ✅ Blade Directives Auto-Registration
- ✅ Services Auto-Registration
- ✅ Singletons Auto-Registration
- ✅ Package Information Auto-Registration
- ✅ Support Information Auto-Registration
- ✅ Funding Information Auto-Registration
- ✅ Keywords Auto-Registration

## 🚀 Quick Start

Once the package is installed, everything is automatically available:

```php
// Use Alert facade
use Wahyudedik\LaravelAlert\Facades\Alert;

Alert::success('Welcome to Laravel Alert!');
```

```blade
{{-- Use Blade components --}}
<x-alert type="success" message="Hello World!" />
<x-alerts />
```

```bash
# Use console commands
php artisan laravel-alert:install
php artisan laravel-alert:publish
php artisan laravel-alert:clear
```

## 🔍 Troubleshooting

If auto-discovery is not working, check the following:

1. **Composer Autoload**: Make sure Composer autoload is up to date:
   ```bash
   composer dump-autoload
   ```

2. **Laravel Cache**: Clear Laravel cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Package Installation**: Verify the package is properly installed:
   ```bash
   composer show wahyudedik/laravel-alert
   ```

4. **Service Provider**: Check if the service provider is registered:
   ```bash
   php artisan package:discover
   ```

5. **Configuration**: Verify configuration is available:
   ```bash
   php artisan config:show laravel-alert
   ```

## 📚 Additional Resources

- [Complete Documentation](https://wahyudedik.github.io/LaravelAlert)
- [API Reference](https://wahyudedik.github.io/LaravelAlert/api-reference)
- [Examples](https://wahyudedik.github.io/LaravelAlert/examples)
- [GitHub Repository](https://github.com/wahyudedik/LaravelAlert)
- [Packagist](https://packagist.org/packages/wahyudedik/laravel-alert)
- [Issues](https://github.com/wahyudedik/LaravelAlert/issues)
- [Discussions](https://github.com/wahyudedik/LaravelAlert/discussions)
