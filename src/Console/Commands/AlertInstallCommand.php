<?php

namespace Wahyudedik\LaravelAlert\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AlertInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'alert:install 
                            {--force : Force the installation even if files already exist}
                            {--theme= : Specify the default theme (bootstrap, tailwind, bulma)}';

    /**
     * The console command description.
     */
    protected $description = 'Install Laravel Alert package and publish resources';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Installing Laravel Alert package...');

        // Publish configuration
        $this->call('vendor:publish', [
            '--provider' => 'Wahyudedik\LaravelAlert\AlertServiceProvider',
            '--tag' => 'laravel-alert-config',
            '--force' => $this->option('force')
        ]);

        // Publish views
        $this->call('vendor:publish', [
            '--provider' => 'Wahyudedik\LaravelAlert\AlertServiceProvider',
            '--tag' => 'laravel-alert-views',
            '--force' => $this->option('force')
        ]);

        // Publish assets
        $this->call('vendor:publish', [
            '--provider' => 'Wahyudedik\LaravelAlert\AlertServiceProvider',
            '--tag' => 'laravel-alert-assets',
            '--force' => $this->option('force')
        ]);

        // Set default theme if specified
        if ($theme = $this->option('theme')) {
            $this->setDefaultTheme($theme);
        }

        // Create example usage file
        $this->createExampleUsage();

        // Add middleware to kernel
        $this->addMiddlewareToKernel();

        $this->info('Laravel Alert package installed successfully!');
        $this->line('');
        $this->line('Next steps:');
        $this->line('1. Add <x-alerts /> to your main layout file');
        $this->line('2. Use Alert::success("Message") in your controllers');
        $this->line('3. Customize the configuration in config/laravel-alert.php');
        $this->line('4. Run "php artisan alert:publish" to customize views');

        return 0;
    }

    /**
     * Set the default theme in configuration.
     */
    protected function setDefaultTheme(string $theme): void
    {
        $configPath = config_path('laravel-alert.php');

        if (File::exists($configPath)) {
            $content = File::get($configPath);
            $content = str_replace(
                "'default_theme' => 'bootstrap'",
                "'default_theme' => '{$theme}'",
                $content
            );
            File::put($configPath, $content);

            $this->info("Default theme set to: {$theme}");
        }
    }

    /**
     * Create example usage file.
     */
    protected function createExampleUsage(): void
    {
        $exampleContent = <<<'PHP'
<?php

// Example usage of Laravel Alert package

use Wahyudedik\LaravelAlert\Facades\Alert;

// Basic usage
Alert::success('Operation completed successfully!');
Alert::error('Something went wrong!');
Alert::warning('Please check your input.');
Alert::info('Welcome to our application!');

// With titles
Alert::success('User created successfully!', 'Success');
Alert::error('Failed to save data', 'Error');

// With custom options
Alert::success('Data saved!', 'Success', [
    'dismissible' => true,
    'icon' => 'fas fa-check',
    'class' => 'custom-success-class',
    'style' => 'border-left: 4px solid #28a745;'
]);

// Temporary alerts
Alert::temporary('info', 'This alert will expire in 5 minutes', 'Temporary', 300);

// Flash alerts
Alert::flash('success', 'Operation completed!', 'Success', 3000);

// In Blade templates:
// <x-alerts />
// @alert('success', 'Message')
// @alerts
PHP;

        File::put(base_path('laravel-alert-example.php'), $exampleContent);
        $this->info('Example usage file created: laravel-alert-example.php');
    }

    /**
     * Add middleware to kernel.
     */
    protected function addMiddlewareToKernel(): void
    {
        $kernelPath = app_path('Http/Kernel.php');

        if (File::exists($kernelPath)) {
            $content = File::get($kernelPath);

            // Check if middleware is already added
            if (strpos($content, "'alert' => \\Wahyudedik\\LaravelAlert\\Middleware\\AlertMiddleware::class") === false) {
                // Add to $middlewareAliases array
                $content = str_replace(
                    'protected $middlewareAliases = [',
                    "protected \$middlewareAliases = [\n        'alert' => \\Wahyudedik\\LaravelAlert\\Middleware\\AlertMiddleware::class,",
                    $content
                );

                File::put($kernelPath, $content);
                $this->info('Middleware added to Kernel.php');
            }
        }
    }
}
