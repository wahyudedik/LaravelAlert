# IDE Support

Laravel Alert provides comprehensive IDE support for both PhpStorm and VS Code, offering enhanced autocompletion, type hints, documentation, and code snippets.

## 🚀 Features

### PhpStorm Support
- ✅ **Live Templates** - Quick code generation
- ✅ **Type Hints** - Full type information
- ✅ **Autocompletion** - Intelligent code completion
- ✅ **Documentation** - Inline documentation
- ✅ **Inspections** - Code quality checks
- ✅ **Metadata** - Enhanced IntelliSense

### VS Code Support
- ✅ **Code Snippets** - Quick code generation
- ✅ **IntelliSense** - Smart code completion
- ✅ **Type Definitions** - Type information
- ✅ **Hover Information** - Inline documentation
- ✅ **Parameter Hints** - Method signatures
- ✅ **Settings** - Optimized configuration

## 📁 Files Included

### PhpStorm Files
- `.idea/laravel-alert.xml` - PhpStorm configuration
- `.phpstorm.meta.php` - Metadata for autocompletion
- `src/TypeHints.php` - Comprehensive type hints

### VS Code Files
- `.vscode/laravel-alert.code-snippets` - Code snippets
- `.vscode/settings.json` - VS Code configuration

## 🔧 PhpStorm Setup

### Automatic Setup
The package includes automatic PhpStorm configuration that will be applied when you open the project.

### Manual Setup (if needed)
1. Open PhpStorm Settings
2. Go to `Editor > Live Templates`
3. Import `laravel-alert.xml` template group
4. Enable Laravel Alert inspections

### Live Templates
Use these prefixes to trigger live templates:

```php
// Basic alerts
laravel-alert-success → Alert::success('message');
laravel-alert-error → Alert::error('message');
laravel-alert-warning → Alert::warning('message');
laravel-alert-info → Alert::info('message');

// Toast alerts
laravel-alert-toast → Toast::success('message');

// Modal alerts
laravel-alert-modal → Modal::success('message');

// Inline alerts
laravel-alert-inline → Inline::success('message');
```

### Blade Templates
```blade
{{-- Blade components --}}
laravel-alert-blade → <x-alert type="success" message="message" />
laravel-alert-blade-all → <x-alerts />
```

## 🔧 VS Code Setup

### Automatic Setup
The package includes VS Code configuration that will be applied automatically.

### Recommended Extensions
```json
{
  "recommendations": [
    "bmewburn.vscode-intelephense-client",
    "shufo.vscode-blade-formatter",
    "onecentlin.laravel-blade",
    "ryannaddy.laravel-artisan",
    "codingyu.laravel-goto-view",
    "amiralizadeh9480.laravel-extra-intellisense"
  ]
}
```

### Code Snippets
Use these prefixes to trigger code snippets:

```php
// Basic alerts
alert-success → Alert::success('message');
alert-error → Alert::error('message');
alert-warning → Alert::warning('message');
alert-info → Alert::info('message');
alert-custom → Alert::custom('type', 'message', 'title');

// Toast alerts
toast-success → Toast::success('message');
toast-error → Toast::error('message');
toast-warning → Toast::warning('message');
toast-info → Toast::info('message');
toast-custom → Toast::custom('type', 'message', 'title');

// Modal alerts
modal-success → Modal::success('message');
modal-error → Modal::error('message');
modal-warning → Modal::warning('message');
modal-info → Modal::info('message');
modal-custom → Modal::custom('type', 'message', 'title');

// Inline alerts
inline-success → Inline::success('message');
inline-error → Inline::error('message');
inline-warning → Inline::warning('message');
inline-info → Inline::info('message');
inline-custom → Inline::custom('type', 'message', 'title');

// Blade components
x-alert → <x-alert type="success" message="message" />
x-alerts → <x-alerts />
x-alert-toast → <x-alert-toast type="success" message="message" />
x-alert-modal → <x-alert-modal type="success" message="message" />
x-alert-inline → <x-alert-inline type="success" message="message" />

// Fluent API
alert-fluent → Alert::success('message')->title('title')->dismissible(true)...

// Advanced features
alert-ajax → Alert::ajax('success', 'message');
alert-websocket → Alert::websocket('info', 'message');
alert-pusher → Alert::pusher('warning', 'message');
alert-email → Alert::email('error', 'message');
alert-bulk → Alert::bulk([...]);

// Utility methods
alert-clear → Alert::clear();
alert-clear-all → Alert::clearAll();
alert-count → Alert::count();
alert-has → Alert::has('type');
alert-get → Alert::get('type');
alert-all → Alert::all();
alert-render → Alert::render('type');
alert-render-all → Alert::renderAll();
```

## 💡 Usage Tips

### Type Hints
All Laravel Alert classes include comprehensive type hints:

```php
use Wahyudedik\LaravelAlert\Facades\Alert;

// IDE will provide autocompletion for all methods
Alert::success('message')
    ->title('title')           // ← Type hint: string
    ->dismissible(true)        // ← Type hint: bool
    ->autoDismiss(5000)       // ← Type hint: int
    ->position('top-right')    // ← Type hint: string
    ->theme('bootstrap')       // ← Type hint: string
    ->animation('fadeIn')      // ← Type hint: string
    ->icon('check')           // ← Type hint: string
    ->context('form')         // ← Type hint: string
    ->field('email')          // ← Type hint: string
    ->priority('normal')      // ← Type hint: string
    ->expiresAt(now())        // ← Type hint: DateTime
    ->withData(['key' => 'value'])     // ← Type hint: array
    ->withActions([...]);              // ← Type hint: array
```

### Documentation
Hover over any method to see comprehensive documentation:

```php
/**
 * Create a success alert
 * 
 * @param string $message The alert message
 * @param string|null $title Optional alert title
 * @param array $options Additional options
 * @return Alert The alert instance for method chaining
 */
Alert::success($message, $title, $options);
```

### Autocompletion
IDE will provide intelligent suggestions:

```php
Alert::        // ← Shows: success, error, warning, info, custom, ajax, etc.
  ->theme('    // ← Shows: bootstrap, tailwind, bulma
  ->position(' // ← Shows: top-right, top-left, bottom-right, etc.
  ->animation(' // ← Shows: fadeIn, slideIn, bounceIn, etc.
  ->icon('     // ← Shows: check, times, exclamation, info, etc.
```

### Parameter Hints
IDE will show parameter information:

```php
Alert::custom(
  string $type,      // ← Parameter hint
  string $message,   // ← Parameter hint
  string $title,     // ← Parameter hint
  array $options     // ← Parameter hint
);
```

## 🧪 Testing IDE Support

### PhpStorm Testing
1. Type `laravel-alert-` and press `Tab`
2. Select a live template
3. Fill in the parameters
4. Verify autocompletion works

### VS Code Testing
1. Type `alert-` and press `Tab`
2. Select a code snippet
3. Fill in the parameters
4. Verify IntelliSense works

## 🔍 Troubleshooting

### PhpStorm Issues
- **No autocompletion**: Clear caches (`File > Invalidate Caches`)
- **Missing templates**: Restart PhpStorm
- **Type hints not working**: Check if `.phpstorm.meta.php` exists

### VS Code Issues
- **No snippets**: Check if `.vscode/laravel-alert.code-snippets` exists
- **No IntelliSense**: Install Intelephense extension
- **No type hints**: Check if `src/TypeHints.php` exists

## 📚 Advanced Configuration

### Custom Templates (PhpStorm)
Add your own live templates in PhpStorm Settings:

1. Go to `Editor > Live Templates`
2. Select `Laravel Alert` group
3. Add new template
4. Configure abbreviation and template text

### Custom Snippets (VS Code)
Add your own snippets in `.vscode/laravel-alert.code-snippets`:

```json
{
  "My Custom Alert": {
    "prefix": "my-alert",
    "body": [
      "Alert::$1('$2')->theme('$3');"
    ],
    "description": "My custom alert snippet"
  }
}
```

### Extension Settings (VS Code)
Customize VS Code settings in `.vscode/settings.json`:

```json
{
  "php.suggest.basic": true,
  "laravel.enableAutocompletion": true,
  "editor.quickSuggestions": {
    "other": true,
    "comments": false,
    "strings": true
  }
}
```

## 📖 Additional Resources

- [PhpStorm Documentation](https://www.jetbrains.com/help/phpstorm/)
- [VS Code PHP Documentation](https://code.visualstudio.com/docs/languages/php)
- [Laravel Alert Documentation](https://wahyudedik.github.io/LaravelAlert)
- [GitHub Repository](https://github.com/wahyudedik/LaravelAlert)
