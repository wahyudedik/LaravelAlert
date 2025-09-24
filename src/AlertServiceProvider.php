<?php

namespace Wahyudedik\LaravelAlert;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Wahyudedik\LaravelAlert\Managers\AlertManager;
use Wahyudedik\LaravelAlert\View\Components\AlertComponent;
use Wahyudedik\LaravelAlert\View\Components\AlertsComponent;
use Wahyudedik\LaravelAlert\Middleware\AlertMiddleware;
use Wahyudedik\LaravelAlert\Console\Commands\AlertInstallCommand;
use Wahyudedik\LaravelAlert\Console\Commands\AlertPublishCommand;
use Wahyudedik\LaravelAlert\Console\Commands\AlertClearCommand;

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
        // Publish configuration and views
        $this->publishResources();

        // Register Blade components
        $this->registerBladeComponents();

        // Register Blade directives
        $this->registerBladeDirectives();

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-alert');

        // Register middleware
        $this->registerMiddleware();

        // Register console commands
        $this->registerConsoleCommands();

        // Register routes
        $this->registerRoutes();

        // Share global view data
        $this->shareViewData();
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
     * Publish package resources.
     */
    protected function publishResources(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish configuration
            $this->publishes([
                __DIR__ . '/../config/laravel-alert.php' => config_path('laravel-alert.php'),
            ], 'laravel-alert-config');

            // Publish views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/laravel-alert'),
            ], 'laravel-alert-views');

            // Publish assets
            $this->publishes([
                __DIR__ . '/../resources/assets' => public_path('vendor/laravel-alert'),
            ], 'laravel-alert-assets');

            // Publish all resources
            $this->publishes([
                __DIR__ . '/../config/laravel-alert.php' => config_path('laravel-alert.php'),
                __DIR__ . '/../resources/views' => resource_path('views/vendor/laravel-alert'),
                __DIR__ . '/../resources/assets' => public_path('vendor/laravel-alert'),
            ], 'laravel-alert');
        }
    }

    /**
     * Register Blade components.
     */
    protected function registerBladeComponents(): void
    {
        $this->loadViewComponentsAs('alert', [
            'alert' => AlertComponent::class,
            'alerts' => AlertsComponent::class,
        ]);
    }

    /**
     * Register middleware.
     */
    protected function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('alert', AlertMiddleware::class);
    }

    /**
     * Register console commands.
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AlertInstallCommand::class,
                AlertPublishCommand::class,
                AlertClearCommand::class,
            ]);
        }
    }

    /**
     * Register routes.
     */
    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => 'laravel-alert',
            'namespace' => 'Wahyudedik\LaravelAlert\Http\Controllers',
            'middleware' => ['web'],
        ], function () {
            // Basic alert routes
            Route::get('alerts', 'AlertController@index')->name('laravel-alert.alerts');
            Route::post('alerts/dismiss', 'AlertController@dismiss')->name('laravel-alert.dismiss');
            Route::post('alerts/dismiss-all', 'AlertController@dismissAll')->name('laravel-alert.dismiss-all');
            Route::delete('alerts/clear', 'AlertController@clear')->name('laravel-alert.clear');

            // AJAX alert routes
            Route::group(['prefix' => 'ajax'], function () {
                Route::get('alerts', 'AjaxAlertController@index')->name('laravel-alert.ajax.alerts');
                Route::post('alerts', 'AjaxAlertController@store')->name('laravel-alert.ajax.store');
                Route::post('alerts/dismiss', 'AjaxAlertController@dismiss')->name('laravel-alert.ajax.dismiss');
                Route::post('alerts/dismiss-all', 'AjaxAlertController@dismissAll')->name('laravel-alert.ajax.dismiss-all');
                Route::delete('alerts/clear', 'AjaxAlertController@clearByType')->name('laravel-alert.ajax.clear-by-type');
                Route::get('alerts/by-type', 'AjaxAlertController@getByType')->name('laravel-alert.ajax.get-by-type');
                Route::get('alerts/stats', 'AjaxAlertController@stats')->name('laravel-alert.ajax.stats');
                Route::post('alerts/multiple', 'AjaxAlertController@createMultiple')->name('laravel-alert.ajax.create-multiple');
            });

            // WebSocket alert routes
            Route::group(['prefix' => 'ws'], function () {
                Route::post('connect', 'WebSocketAlertController@handleConnection')->name('laravel-alert.ws.connect');
                Route::post('broadcast', 'WebSocketAlertController@broadcast')->name('laravel-alert.ws.broadcast');
                Route::post('subscribe', 'WebSocketAlertController@subscribe')->name('laravel-alert.ws.subscribe');
                Route::post('unsubscribe', 'WebSocketAlertController@unsubscribe')->name('laravel-alert.ws.unsubscribe');
                Route::get('connections', 'WebSocketAlertController@getConnections')->name('laravel-alert.ws.connections');
                Route::post('send', 'WebSocketAlertController@sendToConnection')->name('laravel-alert.ws.send');
            });
        });
    }

    /**
     * Share view data globally.
     */
    protected function shareViewData(): void
    {
        View::composer('*', function ($view) {
            $view->with('laravelAlertConfig', config('laravel-alert', []));
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
