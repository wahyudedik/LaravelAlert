<?php

namespace Tests\Discovery;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Wahyudedik\LaravelAlert\AlertServiceProvider;

class PackageDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_auto_discover_service_provider()
    {
        $this->assertTrue(
            App::getProviders(AlertServiceProvider::class) !== null,
            'AlertServiceProvider should be auto-discovered'
        );
    }

    /** @test */
    public function it_can_auto_discover_facades()
    {
        // Test Alert facade
        $this->assertTrue(
            class_exists('Wahyudedik\\LaravelAlert\\Facades\\Alert'),
            'Alert facade should be auto-discovered'
        );

        // Test Toast facade
        $this->assertTrue(
            class_exists('Wahyudedik\\LaravelAlert\\Facades\\Toast'),
            'Toast facade should be auto-discovered'
        );

        // Test Modal facade
        $this->assertTrue(
            class_exists('Wahyudedik\\LaravelAlert\\Facades\\Modal'),
            'Modal facade should be auto-discovered'
        );

        // Test Inline facade
        $this->assertTrue(
            class_exists('Wahyudedik\\LaravelAlert\\Facades\\Inline'),
            'Inline facade should be auto-discovered'
        );
    }

    /** @test */
    public function it_can_auto_discover_console_commands()
    {
        $commands = [
            'laravel-alert:install',
            'laravel-alert:publish',
            'laravel-alert:clear',
        ];

        foreach ($commands as $command) {
            $this->assertTrue(
                $this->app->has($command),
                "Command {$command} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_middleware()
    {
        $middleware = [
            'alert',
            'laravel-alert.api.auth',
            'laravel-alert.admin.auth',
            'laravel-alert.webhook.auth',
            'laravel-alert.cors',
        ];

        foreach ($middleware as $middlewareName) {
            $this->assertTrue(
                $this->app->has($middlewareName),
                "Middleware {$middlewareName} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_routes()
    {
        // Test API routes
        $this->assertTrue(
            file_exists(base_path('routes/api.php')),
            'API routes should be auto-discovered'
        );

        // Test web routes
        $this->assertTrue(
            file_exists(base_path('routes/web.php')),
            'Web routes should be auto-discovered'
        );
    }

    /** @test */
    public function it_can_auto_discover_views()
    {
        $views = [
            'laravel-alert::components.alert',
            'laravel-alert::components.alerts',
            'laravel-alert::components.toast',
            'laravel-alert::components.modal',
            'laravel-alert::components.inline',
        ];

        foreach ($views as $view) {
            $this->assertTrue(
                view()->exists($view),
                "View {$view} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_assets()
    {
        $assets = [
            'resources/css/laravel-alert.css',
            'resources/css/themes/bootstrap.css',
            'resources/css/themes/tailwind.css',
            'resources/css/themes/bulma.css',
            'resources/js/laravel-alert.js',
            'resources/js/ajax.js',
            'resources/js/websocket.js',
            'resources/js/pusher.js',
        ];

        foreach ($assets as $asset) {
            $this->assertTrue(
                file_exists(base_path($asset)),
                "Asset {$asset} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_config()
    {
        $this->assertTrue(
            Config::has('laravel-alert'),
            'Laravel Alert config should be auto-discovered'
        );

        $this->assertTrue(
            Config::has('laravel-alert.theme'),
            'Theme config should be auto-discovered'
        );

        $this->assertTrue(
            Config::has('laravel-alert.position'),
            'Position config should be auto-discovered'
        );

        $this->assertTrue(
            Config::has('laravel-alert.dismissible'),
            'Dismissible config should be auto-discovered'
        );
    }

    /** @test */
    public function it_can_auto_discover_migrations()
    {
        $migrations = [
            'database/migrations/2024_01_01_000000_create_alerts_table.php',
        ];

        foreach ($migrations as $migration) {
            $this->assertTrue(
                file_exists(base_path($migration)),
                "Migration {$migration} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_translations()
    {
        $translations = [
            'resources/lang/en/laravel-alert.php',
            'resources/lang/es/laravel-alert.php',
            'resources/lang/fr/laravel-alert.php',
            'resources/lang/de/laravel-alert.php',
        ];

        foreach ($translations as $translation) {
            $this->assertTrue(
                file_exists(base_path($translation)),
                "Translation {$translation} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_blade_components()
    {
        $components = [
            'x-alert',
            'x-alerts',
            'x-alert-toast',
            'x-alert-modal',
            'x-alert-inline',
        ];

        foreach ($components as $component) {
            $this->assertTrue(
                $this->app->has($component),
                "Blade component {$component} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_blade_directives()
    {
        $directives = [
            'alert',
            'alerts',
            'alertIf',
        ];

        foreach ($directives as $directive) {
            $this->assertTrue(
                $this->app->has($directive),
                "Blade directive {$directive} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_services()
    {
        $services = [
            'laravel-alert.manager',
            'laravel-alert.toast',
            'laravel-alert.modal',
            'laravel-alert.inline',
            'laravel-alert.database',
            'laravel-alert.redis',
            'laravel-alert.cache',
            'laravel-alert.pusher',
            'laravel-alert.websocket',
            'laravel-alert.email',
            'laravel-alert.performance',
            'laravel-alert.animation',
        ];

        foreach ($services as $service) {
            $this->assertTrue(
                $this->app->has($service),
                "Service {$service} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_singletons()
    {
        $singletons = [
            'laravel-alert.manager',
            'laravel-alert.toast',
            'laravel-alert.modal',
            'laravel-alert.inline',
            'laravel-alert.database',
            'laravel-alert.redis',
            'laravel-alert.cache',
            'laravel-alert.pusher',
            'laravel-alert.websocket',
            'laravel-alert.email',
            'laravel-alert.performance',
            'laravel-alert.animation',
        ];

        foreach ($singletons as $singleton) {
            $this->assertTrue(
                $this->app->isSingleton($singleton),
                "Singleton {$singleton} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_package_information()
    {
        $packageInfo = [
            'name' => 'wahyudedik/laravel-alert',
            'description' => 'A comprehensive Laravel alert system with multiple types, themes, and integrations',
            'version' => '1.0.0',
            'author' => 'Wahyudedik',
            'email' => 'wahyudedik@gmail.com',
            'homepage' => 'https://github.com/wahyudedik/LaravelAlert',
            'license' => 'MIT',
        ];

        foreach ($packageInfo as $key => $value) {
            $this->assertEquals(
                $value,
                Config::get("laravel-alert-discovery.package.{$key}"),
                "Package {$key} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_support_information()
    {
        $supportInfo = [
            'email' => 'wahyudedik@gmail.com',
            'issues' => 'https://github.com/wahyudedik/LaravelAlert/issues',
            'source' => 'https://github.com/wahyudedik/LaravelAlert',
            'docs' => 'https://wahyudedik.github.io/LaravelAlert',
        ];

        foreach ($supportInfo as $key => $value) {
            $this->assertEquals(
                $value,
                Config::get("laravel-alert-discovery.package.support.{$key}"),
                "Support {$key} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_funding_information()
    {
        $fundingInfo = [
            'github' => 'https://github.com/sponsors/wahyudedik',
        ];

        foreach ($fundingInfo as $key => $value) {
            $this->assertEquals(
                $value,
                Config::get("laravel-alert-discovery.package.funding.{$key}"),
                "Funding {$key} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_keywords()
    {
        $keywords = [
            'laravel',
            'alert',
            'notification',
            'toast',
            'modal',
            'inline',
            'blade',
            'javascript',
            'ajax',
            'websocket',
            'pusher',
            'email',
            'api',
            'rest',
            'real-time',
            'bootstrap',
            'tailwind',
            'bulma',
            'responsive',
            'accessible',
            'i18n',
            'customizable',
            'performance',
            'optimized',
            'testing',
            'documentation',
        ];

        $discoveredKeywords = Config::get('laravel-alert-discovery.package.keywords', []);

        foreach ($keywords as $keyword) {
            $this->assertContains(
                $keyword,
                $discoveredKeywords,
                "Keyword {$keyword} should be auto-discovered"
            );
        }
    }

    /** @test */
    public function it_can_auto_discover_auto_discovery_configuration()
    {
        $this->assertTrue(
            Config::get('laravel-alert-discovery.auto_discovery.enabled'),
            'Auto-discovery should be enabled'
        );

        $this->assertTrue(
            Config::get('laravel-alert-discovery.service_provider.auto_register'),
            'Service provider should be auto-registered'
        );

        $this->assertTrue(
            Config::get('laravel-alert-discovery.facades.auto_register'),
            'Facades should be auto-registered'
        );

        $this->assertTrue(
            Config::get('laravel-alert-discovery.commands.auto_register'),
            'Commands should be auto-registered'
        );

        $this->assertTrue(
            Config::get('laravel-alert-discovery.middleware.auto_register'),
            'Middleware should be auto-registered'
        );

        $this->assertTrue(
            Config::get('laravel-alert-discovery.routes.auto_register'),
            'Routes should be auto-registered'
        );

        $this->assertTrue(
            Config::get('laravel-alert-discovery.views.auto_register'),
            'Views should be auto-registered'
        );

        $this->assertTrue(
            Config::get('laravel-alert-discovery.assets.auto_register'),
            'Assets should be auto-registered'
        );

        $this->assertTrue(
            Config::get('laravel-alert-discovery.config.auto_register'),
            'Config should be auto-registered'
        );

        $this->assertTrue(
            Config::get('laravel-alert-discovery.database.auto_register'),
            'Database should be auto-registered'
        );

        $this->assertTrue(
            Config::get('laravel-alert-discovery.translations.auto_register'),
            'Translations should be auto-registered'
        );
    }
}
