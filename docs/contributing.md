# Contributing Guide

Thank you for your interest in contributing to the Laravel Alert library! This guide will help you get started with contributing to the project.

## 🤝 How to Contribute

### 1. Fork the Repository

First, fork the repository on GitHub:

```bash
# Clone your fork
git clone https://github.com/your-username/LaravelAlert.git
cd LaravelAlert

# Add upstream remote
git remote add upstream https://github.com/wahyudedik/LaravelAlert.git
```

### 2. Create a Branch

Create a new branch for your feature or bugfix:

```bash
git checkout -b feature/your-feature-name
# or
git checkout -b bugfix/your-bugfix-name
```

### 3. Make Your Changes

Make your changes and ensure they follow the project's coding standards:

- Follow PSR-12 coding standards
- Write clear, descriptive commit messages
- Add tests for new functionality
- Update documentation as needed

### 4. Test Your Changes

Run the test suite to ensure your changes don't break existing functionality:

```bash
# Run all tests
composer test

# Run specific test
php artisan test --filter=AlertManagerTest

# Run with coverage
composer test-coverage
```

### 5. Submit a Pull Request

Push your changes and submit a pull request:

```bash
git push origin feature/your-feature-name
```

## 📋 Contribution Guidelines

### Code Standards

- **PHP**: Follow PSR-12 coding standards
- **JavaScript**: Use ES6+ features and follow modern practices
- **CSS**: Use consistent naming conventions
- **Documentation**: Write clear, concise documentation

### Commit Message Format

Use the following format for commit messages:

```
type(scope): description

[optional body]

[optional footer]
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes
- `refactor`: Code refactoring
- `test`: Test changes
- `chore`: Maintenance tasks

**Examples:**
```
feat(api): add bulk alert creation endpoint
fix(blade): resolve component rendering issue
docs(api): update authentication examples
test(manager): add performance optimization tests
```

### Pull Request Guidelines

1. **Title**: Use a clear, descriptive title
2. **Description**: Explain what the PR does and why
3. **Tests**: Ensure all tests pass
4. **Documentation**: Update relevant documentation
5. **Breaking Changes**: Clearly mark any breaking changes

### Issue Guidelines

When creating an issue:

1. **Use the issue template**
2. **Provide clear reproduction steps**
3. **Include relevant code examples**
4. **Specify your environment details**
5. **Add screenshots if applicable**

## 🧪 Testing

### Running Tests

```bash
# Install dependencies
composer install

# Run all tests
composer test

# Run specific test suite
php artisan test --filter=AlertManagerTest
php artisan test --filter=BladeIntegrationTest
php artisan test --filter=ServiceProviderTest

# Run with coverage
composer test-coverage

# Run performance tests
composer test-performance
```

### Writing Tests

When adding new features, include comprehensive tests:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Wahyudedik\LaravelAlert\Facades\Alert;

class NewFeatureTest extends TestCase
{
    public function test_new_feature_works_correctly()
    {
        // Arrange
        $expectedResult = 'expected result';
        
        // Act
        $result = Alert::newFeature();
        
        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    public function test_new_feature_handles_edge_cases()
    {
        // Test edge cases
        $this->expectException(\InvalidArgumentException::class);
        Alert::newFeature('invalid input');
    }
}
```

### Test Coverage

Maintain high test coverage:

- **Unit Tests**: Test individual methods and classes
- **Integration Tests**: Test component interactions
- **Feature Tests**: Test end-to-end functionality
- **Performance Tests**: Test performance characteristics

## 📚 Documentation

### Updating Documentation

When adding new features:

1. **Update README.md** if needed
2. **Add API documentation** for new endpoints
3. **Update examples** with new usage patterns
4. **Add migration guides** for breaking changes

### Documentation Structure

```
docs/
├── README.md              # Main documentation
├── installation.md        # Installation guide
├── quick-start.md         # Quick start guide
├── basic-usage.md         # Basic usage examples
├── blade-integration.md   # Blade component guide
├── api.md                 # API documentation
├── examples.md            # Code examples
├── contributing.md        # This file
└── changelog.md           # Version history
```

### Writing Documentation

- **Use clear, concise language**
- **Include code examples**
- **Add screenshots for UI changes**
- **Keep documentation up-to-date**
- **Use proper markdown formatting**

## 🐛 Bug Reports

### Before Reporting

1. **Check existing issues** to avoid duplicates
2. **Search the documentation** for solutions
3. **Test with the latest version**
4. **Try to reproduce the issue**

### Bug Report Template

```markdown
## Bug Description
Brief description of the bug.

## Steps to Reproduce
1. Go to '...'
2. Click on '...'
3. See error

## Expected Behavior
What you expected to happen.

## Actual Behavior
What actually happened.

## Environment
- PHP Version: 8.1
- Laravel Version: 9.0
- Package Version: 1.0.0
- OS: Ubuntu 20.04

## Additional Context
Any other relevant information.
```

## ✨ Feature Requests

### Before Requesting

1. **Check if the feature exists**
2. **Search for similar requests**
3. **Consider if it fits the project scope**
4. **Think about implementation complexity**

### Feature Request Template

```markdown
## Feature Description
Brief description of the feature.

## Use Case
Why is this feature needed?

## Proposed Solution
How should this feature work?

## Alternatives Considered
What other solutions did you consider?

## Additional Context
Any other relevant information.
```

## 🔧 Development Setup

### Prerequisites

- **PHP**: 8.1 or higher
- **Composer**: Latest version
- **Laravel**: 9.0 or higher
- **Node.js**: 16 or higher (for assets)
- **Git**: Latest version

### Local Development

```bash
# Clone the repository
git clone https://github.com/your-username/LaravelAlert.git
cd LaravelAlert

# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run build

# Run tests
composer test
```

### Docker Development

```bash
# Build Docker image
docker build -t laravel-alert .

# Run container
docker run -p 8000:8000 laravel-alert

# Run tests in container
docker exec -it container_name composer test
```

## 🚀 Release Process

### Version Numbering

We use [Semantic Versioning](https://semver.org/):

- **MAJOR**: Breaking changes
- **MINOR**: New features (backward compatible)
- **PATCH**: Bug fixes (backward compatible)

### Release Checklist

- [ ] All tests pass
- [ ] Documentation updated
- [ ] Changelog updated
- [ ] Version bumped
- [ ] Tag created
- [ ] Release notes written

### Creating a Release

```bash
# Update version
composer version:patch  # or minor, major

# Create tag
git tag v1.0.1
git push origin v1.0.1

# Create GitHub release
gh release create v1.0.1 --title "v1.0.1" --notes "Release notes"
```

## 📞 Getting Help

### Community Support

- **GitHub Discussions**: For questions and discussions
- **GitHub Issues**: For bug reports and feature requests
- **Discord**: For real-time chat (if available)
- **Stack Overflow**: Tag questions with `laravel-alert`

### Code Review Process

1. **Automated checks** must pass
2. **Manual review** by maintainers
3. **Testing** in different environments
4. **Documentation** review
5. **Performance** considerations

## 🏆 Recognition

Contributors will be recognized in:

- **README.md** contributors section
- **CHANGELOG.md** for significant contributions
- **GitHub** contributor statistics
- **Release notes** for major contributions

## 📄 License

By contributing to this project, you agree that your contributions will be licensed under the same license as the project (MIT License).

## 🤝 Code of Conduct

Please read and follow our [Code of Conduct](CODE_OF_CONDUCT.md) to ensure a welcoming environment for all contributors.

---

**Thank you for contributing to Laravel Alert! 🎉**
