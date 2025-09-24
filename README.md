# Laravel Alert

A beautiful and easy-to-use alert notification library for Laravel with Blade integration.

## Features

- 🎨 **Multiple Themes**: Bootstrap, Tailwind CSS, and Bulma support
- 🧩 **Blade Components**: Easy-to-use Blade components and directives
- ⚡ **Auto-dismiss**: Configurable auto-dismiss functionality
- 🎭 **Animations**: Smooth fade and slide animations
- 📱 **Responsive**: Mobile-friendly design
- 🔧 **Customizable**: Highly configurable and extensible
- 🚀 **Easy Integration**: Simple facade and service provider

## Installation

```bash
composer require wahyudedik/laravel-alert
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="laravel-alert-config"
```

## Usage

### Using the Facade

```php
use Wahyudedik\LaravelAlert\Facades\Alert;

// Success alert
Alert::success('Operation completed successfully!');

// Error alert
Alert::error('Something went wrong!');

// Warning alert
Alert::warning('Please check your input.');

// Info alert
Alert::info('Welcome to our application!');

// Custom alert
Alert::add('custom', 'This is a custom alert!');
```

### Using Blade Components

```blade
<!-- Single alert -->
<x-alert type="success" dismissible>
    Your changes have been saved!
</x-alert>

<!-- Display all alerts -->
<x-alerts />
```

### Using Blade Directives

```blade
<!-- Single alert directive -->
@alert('info', 'Welcome to our application!')

<!-- Display all alerts -->
@alerts

<!-- Conditional alert -->
@alertIf($user->isNew(), 'success', 'Welcome to our platform!')
```

## Configuration Options

```php
// config/laravel-alert.php
return [
    'default_theme' => 'bootstrap', // bootstrap, tailwind, bulma
    'auto_dismiss' => true,
    'dismiss_delay' => 5000, // milliseconds
    'animation' => 'fade', // fade, slide, bounce, none
    'position' => 'top-right', // top-right, top-left, bottom-right, bottom-left, top-center, bottom-center
    'max_alerts' => 5,
    'session_key' => 'laravel_alerts',
];
```

## Themes

### Bootstrap
```blade
<x-alert type="success">Success message</x-alert>
<x-alert type="error">Error message</x-alert>
<x-alert type="warning">Warning message</x-alert>
<x-alert type="info">Info message</x-alert>
```

### Tailwind CSS
```blade
<x-alert type="success" class="bg-green-50 text-green-800 border-green-200">
    Success message
</x-alert>
```

## Advanced Usage

### Custom Options

```php
Alert::success('Message', 'Title', [
    'dismissible' => true,
    'icon' => 'fas fa-check',
    'class' => 'custom-alert-class',
    'style' => 'border-left: 4px solid #28a745;'
]);
```

### Custom Themes

You can create custom themes by publishing the views:

```bash
php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="laravel-alert-views"
```

## Requirements

- PHP 8.0+
- Laravel 9.0+
- Bootstrap 4/5 (for Bootstrap theme)
- Tailwind CSS (for Tailwind theme)

## License

MIT License. See [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

If you find this package useful, please consider starring the repository on GitHub.
