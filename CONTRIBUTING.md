# Contributing to Laravel Alert

Thank you for your interest in contributing to Laravel Alert! This document provides guidelines and information for contributors.

## 🚀 Getting Started

### Prerequisites
- PHP 8.1 or higher
- Laravel 9.0 or higher
- Composer
- Git
- Node.js (for frontend assets)

### Development Setup

1. **Fork the repository**
   ```bash
   git clone https://github.com/your-username/LaravelAlert.git
   cd LaravelAlert
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Run tests**
   ```bash
   composer test
   ```

4. **Build assets**
   ```bash
   npm run build
   ```

## 📋 Contribution Guidelines

### Code Style
- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Add comprehensive docblocks
- Write unit tests for new features
- Update documentation

### Commit Messages
Use conventional commits format:
```
type(scope): description

feat(alert): add new toast notification type
fix(blade): resolve component rendering issue
docs(readme): update installation instructions
test(alert): add unit tests for new features
```

### Pull Request Process

1. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes**
   - Write code following our style guide
   - Add tests for new functionality
   - Update documentation

3. **Test your changes**
   ```bash
   composer test
   composer lint
   composer stan
   ```

4. **Commit your changes**
   ```bash
   git add .
   git commit -m "feat(alert): add new feature"
   ```

5. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Create a Pull Request**
   - Use the PR template
   - Provide a clear description
   - Link related issues
   - Include screenshots if applicable

## 🐛 Bug Reports

### Before Reporting
- Check existing issues
- Update to the latest version
- Test with a minimal setup

### Bug Report Template
```markdown
**Bug Description**
A clear description of the bug.

**Steps to Reproduce**
1. Go to '...'
2. Click on '....'
3. Scroll down to '....'
4. See error

**Expected Behavior**
What you expected to happen.

**Actual Behavior**
What actually happened.

**Environment**
- Laravel Version: [e.g. 10.0]
- PHP Version: [e.g. 8.1]
- Package Version: [e.g. 1.0.0]
- Browser: [e.g. Chrome 90]

**Additional Context**
Any other context about the problem.
```

## ✨ Feature Requests

### Before Requesting
- Check existing feature requests
- Consider if it fits the project scope
- Think about implementation complexity

### Feature Request Template
```markdown
**Feature Description**
A clear description of the feature.

**Use Case**
Why is this feature needed?

**Proposed Solution**
How would you like this to work?

**Alternatives Considered**
Other solutions you've considered.

**Additional Context**
Any other context about the feature request.
```

## 🧪 Testing

### Running Tests
```bash
# Run all tests
composer test

# Run specific test suite
composer test -- --testsuite=Unit
composer test -- --testsuite=Integration
composer test -- --testsuite=Browser
composer test -- --testsuite=Performance

# Run with coverage
composer test-coverage
```

### Writing Tests
- Write unit tests for all new features
- Test edge cases and error conditions
- Use descriptive test names
- Follow AAA pattern (Arrange, Act, Assert)

### Test Structure
```php
/** @test */
public function it_can_create_success_alert()
{
    // Arrange
    $message = 'Success message';
    
    // Act
    $alert = Alert::success($message);
    
    // Assert
    $this->assertEquals('success', $alert->getType());
    $this->assertEquals($message, $alert->getMessage());
}
```

## 📚 Documentation

### Documentation Guidelines
- Use clear, concise language
- Include code examples
- Add screenshots for UI changes
- Keep documentation up to date

### Documentation Structure
```
docs/
├── README.md              # Main documentation index
├── installation.md        # Installation guide
├── usage.md              # Usage examples
├── configuration.md       # Configuration options
├── themes.md             # Theme customization
├── api.md                # API reference
├── examples.md           # Code examples
├── contributing.md       # Contributing guide
├── changelog.md          # Version history
└── troubleshooting.md    # Common issues
```

## 🎨 Design Guidelines

### UI/UX Principles
- Follow Laravel design patterns
- Maintain consistency with Laravel ecosystem
- Ensure accessibility compliance
- Support responsive design
- Provide clear visual feedback

### Theme Development
- Follow CSS naming conventions
- Use CSS custom properties
- Support dark mode
- Ensure cross-browser compatibility
- Optimize for performance

## 🔧 Development Workflow

### Branch Naming
- `feature/description` - New features
- `bugfix/description` - Bug fixes
- `hotfix/description` - Critical fixes
- `docs/description` - Documentation updates
- `refactor/description` - Code refactoring

### Code Review Process
1. **Self Review** - Review your own code first
2. **Automated Checks** - Ensure all CI checks pass
3. **Peer Review** - Request review from maintainers
4. **Testing** - Verify functionality works as expected
5. **Documentation** - Update relevant documentation

### Release Process
1. **Version Bump** - Update version numbers
2. **Changelog** - Update CHANGELOG.md
3. **Tag Release** - Create git tag
4. **Publish** - Release to Packagist
5. **Documentation** - Update documentation

## 🏷️ Issue Labels

### Bug Labels
- `bug` - Something isn't working
- `critical` - Critical bug affecting core functionality
- `minor` - Minor bug with workaround
- `regression` - Bug introduced in recent changes

### Feature Labels
- `enhancement` - New feature or request
- `good first issue` - Good for newcomers
- `help wanted` - Extra attention needed
- `question` - Further information is requested

### Priority Labels
- `priority: high` - High priority
- `priority: medium` - Medium priority
- `priority: low` - Low priority

### Type Labels
- `type: bug` - Bug report
- `type: feature` - Feature request
- `type: documentation` - Documentation issue
- `type: performance` - Performance issue
- `type: security` - Security issue

## 📞 Support

### Getting Help
- **GitHub Issues** - For bugs and feature requests
- **GitHub Discussions** - For questions and discussions
- **Documentation** - Check the docs first
- **Stack Overflow** - Use `laravel-alert` tag

### Community Guidelines
- Be respectful and inclusive
- Help others learn and grow
- Share knowledge and experience
- Follow the code of conduct
- Report inappropriate behavior

## 🎯 Roadmap

### Current Focus
- Performance optimizations
- Additional themes
- Enhanced JavaScript API
- Better TypeScript support
- Improved accessibility

### Future Plans
- Vue.js integration
- React integration
- Angular integration
- Mobile app support
- Advanced analytics

## 📄 License

By contributing to Laravel Alert, you agree that your contributions will be licensed under the MIT License.

## 🙏 Recognition

Contributors will be recognized in:
- README.md contributors section
- CHANGELOG.md for significant contributions
- Release notes for major contributions
- GitHub contributors page

## 📞 Contact

- **Maintainer**: Wahyudedik
- **Email**: wahyudedik@gmail.com
- **GitHub**: [@wahyudedik](https://github.com/wahyudedik)
- **Twitter**: [@wahyudedik](https://twitter.com/wahyudedik)

Thank you for contributing to Laravel Alert! 🎉
