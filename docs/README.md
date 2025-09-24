# Laravel Alert Documentation

Welcome to the Laravel Alert library documentation. This comprehensive guide will help you get started with the Laravel Alert package and explore all its features.

## 📚 Documentation Structure

### Getting Started
- [Installation Guide](installation.md) - Complete installation and setup guide
- [Quick Start](quick-start.md) - Get up and running quickly
- [Configuration](configuration.md) - Detailed configuration options

### Core Features
- [Basic Usage](basic-usage.md) - Learn the fundamentals
- [Blade Integration](blade-integration.md) - Blade components and directives
- [Advanced Alert Types](advanced-alert-types.md) - Toast, Modal, and Inline alerts

### Customization
- [Theming](theming.md) - Bootstrap, Tailwind, Bulma themes
- [Animations](animations.md) - Animation system and effects
- [JavaScript Integration](javascript-integration.md) - Client-side functionality

### API & Integration
- [REST API](api.md) - Complete API documentation
- [Authentication](authentication.md) - API authentication methods
- [Third-party Integrations](integrations.md) - External service integrations

### Performance & Storage
- [Database Storage](database-storage.md) - Persistent alert storage
- [Cache Integration](cache-integration.md) - Cache-based storage
- [Redis Support](redis-support.md) - High-performance Redis storage
- [Performance Optimization](performance.md) - Performance tuning guide

### Advanced Features
- [Real-time Alerts](real-time-alerts.md) - WebSocket and real-time updates
- [WebSocket Support](websocket.md) - WebSocket implementation
- [Admin Panel](admin-panel.md) - Alert management interface

### Examples & Development
- [Code Examples](examples.md) - Practical usage examples
- [Contributing](contributing.md) - How to contribute to the project
- [Testing](testing.md) - Testing guidelines and examples
- [Changelog](changelog.md) - Version history and changes

## 🚀 Quick Links

- [Installation](installation.md#installation) - Get started quickly
- [Basic Usage](basic-usage.md#basic-usage) - Learn the fundamentals
- [API Reference](api.md#api-reference) - Complete API documentation
- [Examples](examples.md#examples) - Practical code examples

## 📖 Documentation Development

This documentation is built using [MkDocs](https://www.mkdocs.org/) with the [Material theme](https://squidfunk.github.io/mkdocs-material/).

### Local Development

```bash
# Install dependencies
make install

# Start development server
make dev

# Build documentation
make build

# Deploy to GitHub Pages
make deploy
```

### Docker Development

```bash
# Start with Docker
make docker-dev

# Build with Docker
make docker-build

# Deploy with Docker
make docker-deploy
```

### Available Commands

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

### Documentation Structure

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

## 📖 Table of Contents

1. [Installation & Setup](installation.md)
2. [Basic Usage](basic-usage.md)
3. [Blade Components](blade-integration.md)
4. [Advanced Features](advanced-alert-types.md)
5. [API Documentation](api.md)
6. [Performance Guide](performance.md)
7. [Contributing](contributing.md)

## 🎯 Features Overview

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

## 🛠️ Installation

```bash
composer require wahyudedik/laravel-alert
```

```bash
php artisan alert:install
```

## 📝 Basic Usage

```php
use Wahyudedik\LaravelAlert\Facades\Alert;

// Create alerts
Alert::success('Operation completed successfully!');
Alert::error('Something went wrong!');
Alert::warning('Please check your input!');
Alert::info('Here is some information!');

// With title
Alert::success('User created!', 'Success');

// With options
Alert::success('User created!', 'Success', [
    'dismissible' => true,
    'auto_dismiss' => true,
    'auto_dismiss_delay' => 5000
]);
```

## 🎨 Blade Usage

```blade
{{-- Single Alert --}}
<x-alert type="success" message="Operation completed!" />

{{-- Multiple Alerts --}}
<x-alerts />

{{-- With Custom Options --}}
<x-alert 
    type="error" 
    message="Something went wrong!" 
    title="Error"
    dismissible="true"
    auto-dismiss="true"
    auto-dismiss-delay="5000"
/>
```

## 🔧 Configuration

```php
// config/laravel-alert.php
return [
    'default_theme' => 'bootstrap',
    'auto_dismiss' => true,
    'dismiss_delay' => 5000,
    'animation' => 'fade',
    'position' => 'top-right',
    'max_alerts' => 5,
    'session_key' => 'laravel_alerts',
];
```

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

## 🎯 Examples

### Basic Examples
- [Simple Alerts](examples/basic-alerts.md)
- [Form Validation](examples/form-validation.md)
- [AJAX Integration](examples/ajax-integration.md)

### Advanced Examples
- [Real-time Alerts](examples/real-time-alerts.md)
- [Custom Themes](examples/custom-themes.md)
- [Performance Optimization](examples/performance.md)

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](contributing.md) for details.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](../LICENSE) file for details.

## 🆘 Support

- [GitHub Issues](https://github.com/wahyudedik/LaravelAlert/issues)
- [Documentation](https://github.com/wahyudedik/LaravelAlert/docs)
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
