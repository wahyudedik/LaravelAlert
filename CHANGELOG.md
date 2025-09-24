# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release of Laravel Alert library
- Comprehensive alert system with multiple types
- Fluent API for easy configuration
- Blade components and directives
- REST API with authentication
- Real-time notifications via Pusher/WebSocket
- Email notifications
- Performance optimizations
- Comprehensive testing suite
- Complete documentation

### Changed
- N/A

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- N/A

### Security
- N/A

## [1.0.0] - 2024-01-01

### Added
- ✨ **Core Alert System**
  - Success, error, warning, and info alert types
  - Fluent API with method chaining
  - Customizable options (dismissible, auto-dismiss, themes, positions, animations)
  - Session-based storage with fallback options
  - Alert expiration and auto-dismiss functionality
  - Bulk operations for multiple alerts
  - Alert filtering and management

- 🎨 **Blade Integration**
  - `<x-alert>` component for single alerts
  - `<x-alerts>` component for multiple alerts
  - `@alert` directive for inline alerts
  - `@alerts` directive for alert containers
  - `@alertIf` directive for conditional alerts
  - Support for Bootstrap, Tailwind CSS, and Bulma themes

- 🚀 **Advanced Alert Types**
  - Toast alerts with positioning and animations
  - Modal alerts with size and backdrop options
  - Inline alerts for form integration
  - Custom alert types with full customization

- 🎯 **JavaScript Integration**
  - Client-side JavaScript API
  - AJAX endpoints for server communication
  - WebSocket support for real-time updates
  - Pusher integration for real-time notifications
  - Auto-dismiss functionality
  - Alert dismissal handling

- 🔧 **Customization & Theming**
  - Bootstrap theme with responsive design
  - Tailwind CSS theme with utility classes
  - Bulma theme with modern styling
  - Custom CSS support
  - Animation system with predefined effects
  - Icon support with Font Awesome integration

- 📊 **Persistence & Storage**
  - Database storage with Eloquent models
  - Redis storage for high-performance applications
  - Cache storage with multiple drivers
  - Session storage with automatic cleanup
  - Admin panel for alert management
  - Performance optimization features

- 🌐 **API & Integration**
  - REST API with comprehensive endpoints
  - API authentication (Token, API Key, OAuth, JWT)
  - JSON response formatting
  - Rate limiting and security features
  - Email notifications with templates
  - WebSocket integration for real-time updates
  - Pusher integration for real-time notifications

- 🧪 **Testing & Documentation**
  - Unit tests with 100% coverage
  - Integration tests with Laravel
  - Browser tests for JavaScript features
  - Performance tests for optimization
  - Complete API documentation
  - Comprehensive usage examples
  - Step-by-step installation guide

### Changed
- N/A

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- N/A

### Security
- XSS protection for alert content
- CSRF protection for API endpoints
- Rate limiting for API requests
- Input validation and sanitization
- Secure session handling
- Authentication and authorization

## [0.9.0] - 2023-12-15

### Added
- Initial development version
- Basic alert functionality
- Core architecture setup
- Service provider implementation
- Facade system
- Basic Blade components

### Changed
- N/A

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- N/A

### Security
- N/A

## [0.8.0] - 2023-12-01

### Added
- Project initialization
- Composer package setup
- Basic directory structure
- Initial documentation
- GitHub repository setup

### Changed
- N/A

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- N/A

### Security
- N/A

---

## Release Notes

### Version 1.0.0
This is the first stable release of Laravel Alert, featuring a comprehensive alert system with multiple types, themes, and integrations. The library provides a fluent API for easy configuration, Blade components for seamless integration, and a REST API for advanced usage.

### Key Features
- **Multiple Alert Types**: Success, error, warning, and info alerts
- **Fluent API**: Method chaining for easy configuration
- **Blade Integration**: Components and directives for templates
- **JavaScript API**: Client-side functionality
- **REST API**: Comprehensive endpoints with authentication
- **Real-time**: WebSocket and Pusher integration
- **Email**: Notification system with templates
- **Themes**: Bootstrap, Tailwind CSS, and Bulma support
- **Performance**: Optimized for high-traffic applications
- **Testing**: 100% test coverage
- **Documentation**: Complete API reference and examples

### Installation
```bash
composer require wahyudedik/laravel-alert
```

### Quick Start
```php
use Wahyudedik\LaravelAlert\Facades\Alert;

Alert::success('Welcome to Laravel Alert!');
```

### Documentation
- [Complete Documentation](https://wahyudedik.github.io/LaravelAlert)
- [API Reference](https://wahyudedik.github.io/LaravelAlert/api-reference)
- [Examples](https://wahyudedik.github.io/LaravelAlert/examples)

### Support
- [GitHub Issues](https://github.com/wahyudedik/LaravelAlert/issues)
- [Documentation](https://wahyudedik.github.io/LaravelAlert)
- [Email Support](mailto:wahyudedik@gmail.com)

### Contributing
We welcome contributions! Please see our [Contributing Guide](https://github.com/wahyudedik/LaravelAlert/blob/main/CONTRIBUTING.md).

### License
This project is licensed under the MIT License - see the [LICENSE](https://github.com/wahyudedik/LaravelAlert/blob/main/LICENSE) file for details.
