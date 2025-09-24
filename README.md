# Laravel Alert

A comprehensive Laravel package for managing alerts and notifications with support for multiple themes, animations, and real-time updates.

## ✨ Features

- **Multiple Alert Types** - Success, Error, Warning, Info
- **Blade Integration** - Components and Directives
- **Multiple Themes** - Bootstrap, Tailwind, Bulma
- **Animations** - Fade, Slide, Bounce, and more
- **Real-time Updates** - WebSocket and AJAX support
- **Database Storage** - Persistent alert storage
- **Cache Integration** - Redis and Cache support
- **REST API** - Full API support with authentication
- **Admin Panel** - Alert management interface
- **Performance Optimization** - High-performance features

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

## 📚 Documentation

### 📖 Complete Documentation

For comprehensive documentation, visit our [documentation site](https://wahyudedik.github.io/LaravelAlert).

### 📁 Documentation Structure

```
docs/
├── README.md              # Main documentation index
├── installation.md        # Installation guide
├── api.md                 # API documentation
├── examples.md            # Code examples
├── contributing.md        # Contributing guide
├── changelog.md           # Version history
├── mkdocs.yml            # MkDocs configuration
├── package.json          # Node.js dependencies
├── requirements.txt       # Python dependencies
├── Dockerfile            # Docker configuration
├── docker-compose.yml    # Docker Compose configuration
├── Makefile              # Build automation
└── .gitignore            # Git ignore rules
```

### 🛠️ Documentation Development

The documentation is built using [MkDocs](https://www.mkdocs.org/) with the [Material theme](https://squidfunk.github.io/mkdocs-material/).

#### Local Development

```bash
# Navigate to docs directory
cd docs/

# Install dependencies
make install

# Start development server
make dev

# Build documentation
make build

# Deploy to GitHub Pages
make deploy
```

#### Docker Development

```bash
# Start with Docker
make docker-dev

# Build with Docker
make docker-build

# Deploy with Docker
make docker-deploy
```

#### Available Commands

```bash
make help          # Show all available commands
make dev           # Start development server
make build         # Build documentation
make deploy        # Deploy to GitHub Pages
make clean         # Clean build files
make lint          # Lint markdown files
make spell         # Check spelling
make test          # Run tests
make preview       # Preview built documentation
```

## 🎯 Key Features

### Core Features
- ✅ **Multiple Alert Types** - Success, Error, Warning, Info
- ✅ **Blade Integration** - Components and Directives
- ✅ **Multiple Themes** - Bootstrap, Tailwind, Bulma
- ✅ **Animations** - Fade, Slide, Bounce, and more
- ✅ **Auto-dismiss** - Configurable auto-dismissal
- ✅ **Session Storage** - Built-in session management

### Advanced Features
- ✅ **Database Storage** - Persistent alert storage
- ✅ **Cache Integration** - Redis and Cache support
- ✅ **REST API** - Full API support
- ✅ **Real-time Updates** - WebSocket integration
- ✅ **Admin Panel** - Alert management interface
- ✅ **Performance Optimization** - High-performance features

### Integration Features
- ✅ **Third-party Services** - Pusher, WebSocket, Email
- ✅ **Authentication** - Multiple auth methods
- ✅ **Rate Limiting** - Built-in rate limiting
- ✅ **CORS Support** - Cross-origin requests
- ✅ **Webhook Support** - External integrations

## 📡 API Usage

```bash
# Get all alerts
GET /api/v1/alerts

# Create alert
POST /api/v1/alerts
{
    "type": "success",
    "message": "Operation completed!",
    "title": "Success"
}

# Dismiss alert
POST /api/v1/alerts/{id}/dismiss
```

## 🧪 Testing

```bash
# Run tests
composer test

# Run with coverage
composer test-coverage

# Run performance tests
composer test-performance
```

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](docs/contributing.md) for details.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- [GitHub Issues](https://github.com/wahyudedik/LaravelAlert/issues)
- [Documentation](https://wahyudedik.github.io/LaravelAlert)
- [Discussions](https://github.com/wahyudedik/LaravelAlert/discussions)

## 📊 Statistics

- **Version**: 1.0.0
- **PHP Version**: 8.1+
- **Laravel Version**: 9.0+
- **License**: MIT
- **Stars**: ⭐ (GitHub)
- **Downloads**: 📦 (Packagist)

---

**Made with ❤️ by [Wahyudedik](https://github.com/wahyudedik)**
