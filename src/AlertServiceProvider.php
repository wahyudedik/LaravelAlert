<?php

namespace Wahyudedik\LaravelAlert;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Wahyudedik\LaravelAlert\Managers\AlertManager;
use Wahyudedik\LaravelAlert\View\Components\AlertComponent;
use Wahyudedik\LaravelAlert\View\Components\AlertsComponent;

class AlertServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-alert.php',
            'laravel-alert'
        );

        $this->app->singleton(AlertManager::class, function ($app) {
            return new AlertManager($app['session']);
        });

        $this->app->alias(AlertManager::class, 'alert.manager');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel-alert.php' => config_path('laravel-alert.php'),
            ], 'laravel-alert-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/laravel-alert'),
            ], 'laravel-alert-views');
        }

        // Register Blade components
        $this->loadViewComponentsAs('alert', [
            'alert' => AlertComponent::class,
            'alerts' => AlertsComponent::class,
        ]);

        // Register Blade directives
        $this->registerBladeDirectives();

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-alert');
    }

    /**
     * Register custom Blade directives.
     */
    protected function registerBladeDirectives(): void
    {
        Blade::directive('alert', function ($expression) {
            return "<?php echo app('alert.manager')->render($expression); ?>";
        });

        Blade::directive('alerts', function () {
            return "<?php echo app('alert.manager')->renderAll(); ?>";
        });

        Blade::directive('alertIf', function ($expression) {
            return "<?php if($expression) { echo app('alert.manager')->render(func_get_args()); } ?>";
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            AlertManager::class,
            'alert.manager',
        ];
    }
}
