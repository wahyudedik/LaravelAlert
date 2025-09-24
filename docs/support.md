# Support Guide

This guide will help you get the support you need for Laravel Alert.

## 🆘 Getting Help

### Before Asking for Help
1. **Check Documentation**: Look through our comprehensive docs
2. **Search Issues**: Check if your question has been asked before
3. **Search Discussions**: Look through GitHub Discussions
4. **Try Examples**: Test with our code examples
5. **Update Package**: Make sure you're using the latest version

### Where to Get Help

#### GitHub Issues
- **Bug Reports**: Use the [bug report template](https://github.com/wahyudedik/LaravelAlert/issues/new?template=bug_report.md)
- **Feature Requests**: Use the [feature request template](https://github.com/wahyudedik/LaravelAlert/issues/new?template=feature_request.md)
- **Questions**: Use the [question template](https://github.com/wahyudedik/LaravelAlert/issues/new?template=question.md)

#### GitHub Discussions
- **General Questions**: Ask anything about Laravel Alert
- **Showcase**: Share your projects using Laravel Alert
- **Ideas**: Discuss new features and improvements
- **Announcements**: Stay updated with project news

#### Stack Overflow
- Use the `laravel-alert` tag
- Follow Stack Overflow guidelines
- Provide minimal, reproducible examples

#### Documentation
- [Installation Guide](installation.md)
- [Usage Examples](examples.md)
- [API Reference](api.md)
- [Configuration Guide](configuration.md)
- [Themes Guide](themes.md)
- [Troubleshooting Guide](troubleshooting.md)

## 📋 Support Templates

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

### Question Template
```markdown
**Question**
A clear and concise description of your question.

**Context**
Provide context about your question:
- What are you trying to achieve?
- What have you tried so far?
- What specific part do you need help with?

**Code Sample**
```php
// If applicable, provide relevant code
```

**Environment**
- PHP Version: [e.g. 8.1, 8.2]
- Laravel Version: [e.g. 9.0, 10.0, 11.0]
- Package Version: [e.g. 1.0.0]

**Additional Information**
Add any other information that might be helpful.

**Documentation Checked**
- [ ] I have checked the documentation
- [ ] I have searched existing issues
- [ ] I have searched GitHub Discussions
```

## 🔍 Troubleshooting

### Common Issues

#### Installation Issues
```bash
# Clear Composer cache
composer clear-cache

# Update Composer
composer self-update

# Reinstall package
composer remove wahyudedik/laravel-alert
composer require wahyudedik/laravel-alert
```

#### Configuration Issues
```bash
# Publish configuration
php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="config"

# Clear configuration cache
php artisan config:clear
php artisan cache:clear
```

#### View Issues
```bash
# Publish views
php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="views"

# Clear view cache
php artisan view:clear
```

#### Asset Issues
```bash
# Publish assets
php artisan vendor:publish --provider="Wahyudedik\LaravelAlert\AlertServiceProvider" --tag="assets"

# Clear asset cache
php artisan asset:clear
```

### Debug Mode
Enable debug mode to get more information:

```php
// In config/laravel-alert.php
'debug' => true,
'log_level' => 'debug',
```

### Log Files
Check Laravel logs for errors:

```bash
# View logs
tail -f storage/logs/laravel.log

# Clear logs
php artisan log:clear
```

## 🧪 Testing

### Test Your Setup
```bash
# Run package tests
php artisan laravel-alert:test

# Check package status
php artisan laravel-alert:status

# Test specific features
php artisan laravel-alert:test --type=basic
php artisan laravel-alert:test --type=toast
php artisan laravel-alert:test --type=modal
```

### Manual Testing
```php
// Test basic alerts
use Wahyudedik\LaravelAlert\Facades\Alert;

Alert::success('Test alert');
Alert::error('Test error');
Alert::warning('Test warning');
Alert::info('Test info');
```

### Browser Testing
```bash
# Install Laravel Dusk
composer require laravel/dusk --dev

# Run browser tests
php artisan dusk
```

## 📊 Performance

### Performance Monitoring
```bash
# Check performance
php artisan laravel-alert:status --verbose

# Monitor memory usage
php artisan laravel-alert:test --type=performance
```

### Optimization
```php
// In config/laravel-alert.php
'performance' => [
    'enabled' => true,
    'optimize_queries' => true,
    'cache_results' => true,
    'lazy_load' => true,
    'batch_operations' => true,
],
```

## 🔒 Security

### Security Checklist
- [ ] Use HTTPS in production
- [ ] Enable CSRF protection
- [ ] Validate all input
- [ ] Sanitize all output
- [ ] Use secure configuration
- [ ] Keep package updated
- [ ] Monitor for security updates

### Security Issues
Report security issues privately:
- **Email**: wahyudedik@gmail.com
- **GitHub Security**: [Report vulnerability](https://github.com/wahyudedik/LaravelAlert/security)

## 📞 Contact Information

### Support Channels
- **GitHub Issues**: [https://github.com/wahyudedik/LaravelAlert/issues](https://github.com/wahyudedik/LaravelAlert/issues)
- **GitHub Discussions**: [https://github.com/wahyudedik/LaravelAlert/discussions](https://github.com/wahyudedik/LaravelAlert/discussions)
- **Stack Overflow**: [https://stackoverflow.com/questions/tagged/laravel-alert](https://stackoverflow.com/questions/tagged/laravel-alert)
- **Email**: wahyudedik@gmail.com

### Response Times
- **Critical Issues**: Within 24 hours
- **Bug Reports**: Within 48 hours
- **Feature Requests**: Within 1 week
- **Questions**: Within 3 days
- **Documentation**: Within 1 week

## 📚 Resources

### Documentation
- [Installation Guide](installation.md)
- [Usage Examples](examples.md)
- [API Reference](api.md)
- [Configuration Guide](configuration.md)
- [Themes Guide](themes.md)
- [Community Guide](community.md)

### External Resources
- [Laravel Documentation](https://laravel.com/docs)
- [PHP Documentation](https://www.php.net/docs.php)
- [Bootstrap Documentation](https://getbootstrap.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Bulma Documentation](https://bulma.io/documentation)

### Video Tutorials
- [Laravel Alert Tutorial](https://youtube.com/playlist?list=PL)
- [Advanced Usage](https://youtube.com/playlist?list=PL)
- [Customization Guide](https://youtube.com/playlist?list=PL)

## 🎯 Best Practices

### Development
- Use version control
- Write tests
- Follow coding standards
- Document your code
- Use meaningful commit messages

### Deployment
- Test in staging first
- Use secure configuration
- Monitor performance
- Keep backups
- Update regularly

### Maintenance
- Monitor for updates
- Review security advisories
- Test after updates
- Keep documentation current
- Report issues promptly

## 🙏 Thank You

Thank you for using Laravel Alert! We appreciate your feedback and support.

### How to Help
- **Report Bugs**: Help us improve by reporting issues
- **Request Features**: Suggest new functionality
- **Share Knowledge**: Help other users
- **Contribute Code**: Submit pull requests
- **Improve Docs**: Help improve documentation

---

**Remember**: We're here to help! Don't hesitate to reach out if you need assistance. 🚀
