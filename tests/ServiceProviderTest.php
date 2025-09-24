<?php

namespace Wahyudedik\LaravelAlert\Tests;

use Orchestra\Testbench\TestCase;
use Wahyudedik\LaravelAlert\AlertServiceProvider;
use Wahyudedik\LaravelAlert\Facades\Alert;
use Wahyudedik\LaravelAlert\Managers\AlertManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [AlertServiceProvider::class];
    }

    /** @test */
    public function it_registers_alert_manager_in_service_container()
    {
        $this->assertTrue($this->app->bound(AlertManager::class));
        $this->assertTrue($this->app->bound('alert.manager'));

        $alertManager = $this->app->make(AlertManager::class);
        $this->assertInstanceOf(AlertManager::class, $alertManager);
    }

    /** @test */
    public function it_registers_facade()
    {
        $this->assertTrue(class_exists('Wahyudedik\LaravelAlert\Facades\Alert'));

        // Test facade access
        Alert::success('Test message');
        $this->assertCount(1, Alert::getAlerts());
    }

    /** @test */
    public function it_registers_blade_components()
    {
        $this->assertTrue(View::exists('laravel-alert::components.bootstrap.alert'));
        $this->assertTrue(View::exists('laravel-alert::components.bootstrap.alerts'));
        $this->assertTrue(View::exists('laravel-alert::components.tailwind.alert'));
        $this->assertTrue(View::exists('laravel-alert::components.tailwind.alerts'));
        $this->assertTrue(View::exists('laravel-alert::components.bulma.alert'));
    }

    /** @test */
    public function it_registers_blade_directives()
    {
        $directives = Blade::getCustomDirectives();

        $this->assertArrayHasKey('alert', $directives);
        $this->assertArrayHasKey('alerts', $directives);
        $this->assertArrayHasKey('alertIf', $directives);
    }

    /** @test */
    public function it_loads_configuration()
    {
        $config = config('laravel-alert');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('default_theme', $config);
        $this->assertArrayHasKey('auto_dismiss', $config);
        $this->assertArrayHasKey('dismiss_delay', $config);
        $this->assertArrayHasKey('animation', $config);
        $this->assertArrayHasKey('position', $config);
        $this->assertArrayHasKey('max_alerts', $config);
    }

    /** @test */
    public function it_shares_view_data_globally()
    {
        $view = View::make('test-view');
        $view->with('test', 'value');

        $this->assertArrayHasKey('laravelAlertConfig', $view->getData());
    }

    /** @test */
    public function it_can_publish_configuration()
    {
        $this->artisan('vendor:publish', [
            '--provider' => 'Wahyudedik\LaravelAlert\AlertServiceProvider',
            '--tag' => 'laravel-alert-config'
        ]);

        $this->assertFileExists(config_path('laravel-alert.php'));
    }

    /** @test */
    public function it_can_publish_views()
    {
        $this->artisan('vendor:publish', [
            '--provider' => 'Wahyudedik\LaravelAlert\AlertServiceProvider',
            '--tag' => 'laravel-alert-views'
        ]);

        $this->assertDirectoryExists(resource_path('views/vendor/laravel-alert'));
    }

    /** @test */
    public function it_registers_console_commands()
    {
        $this->artisan('list')->assertExitCode(0);

        // Check if our commands are registered
        $this->artisan('alert:install --help')->assertExitCode(0);
        $this->artisan('alert:publish --help')->assertExitCode(0);
        $this->artisan('alert:clear --help')->assertExitCode(0);
    }

    /** @test */
    public function it_registers_middleware()
    {
        $middleware = $this->app['router']->getMiddleware();

        $this->assertArrayHasKey('alert', $middleware);
    }

    /** @test */
    public function it_can_use_fluent_api()
    {
        // Test basic fluent API
        Alert::success('Test message');

        $this->assertCount(1, Alert::getAlerts());

        // Test chaining
        Alert::clear();
        Alert::success('Message 1');
        Alert::error('Message 2');

        $this->assertCount(2, Alert::getAlerts());
    }

    /** @test */
    public function it_handles_session_based_storage()
    {
        // Add alerts
        Alert::success('Session test 1');
        Alert::error('Session test 2');

        $alerts = Alert::getAlerts();
        $this->assertCount(2, $alerts);

        // Clear and verify
        Alert::clear();
        $this->assertCount(0, Alert::getAlerts());
    }

    /** @test */
    public function it_supports_auto_discovery()
    {
        // Test that the package is auto-discoverable
        $this->assertTrue($this->app->bound(AlertManager::class));
        $this->assertTrue($this->app->bound('alert.manager'));
    }

    /** @test */
    public function it_provides_correct_services()
    {
        $provider = new AlertServiceProvider($this->app);
        $services = $provider->provides();

        $this->assertContains(AlertManager::class, $services);
        $this->assertContains('alert.manager', $services);
    }
}
