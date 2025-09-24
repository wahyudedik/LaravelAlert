<?php

namespace Wahyudedik\LaravelAlert\Tests;

use Orchestra\Testbench\TestCase;
use Wahyudedik\LaravelAlert\AlertServiceProvider;
use Wahyudedik\LaravelAlert\Facades\Alert;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewInstance;

class BladeIntegrationTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [AlertServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Set up test configuration
        config([
            'laravel-alert.default_theme' => 'bootstrap',
            'laravel-alert.auto_dismiss' => true,
            'laravel-alert.dismiss_delay' => 5000,
            'laravel-alert.animation' => 'fade',
            'laravel-alert.position' => 'top-right',
            'laravel-alert.max_alerts' => 5,
        ]);
    }

    /** @test */
    public function it_can_render_single_alert_component()
    {
        $view = View::make('laravel-alert::components.bootstrap.alert', [
            'alert' => new \Wahyudedik\LaravelAlert\Models\Alert('success', 'Test message'),
            'config' => config('laravel-alert')
        ]);

        $html = $view->render();

        $this->assertStringContains('alert-success', $html);
        $this->assertStringContains('Test message', $html);
        $this->assertStringContains('role="alert"', $html);
    }

    /** @test */
    public function it_can_render_alerts_component()
    {
        // Add some alerts
        Alert::success('Success message');
        Alert::error('Error message');
        Alert::warning('Warning message');

        $view = View::make('laravel-alert::components.bootstrap.alerts', [
            'alerts' => Alert::getAlerts(),
            'config' => config('laravel-alert')
        ]);

        $html = $view->render();

        $this->assertStringContains('laravel-alerts-container', $html);
        $this->assertStringContains('Success message', $html);
        $this->assertStringContains('Error message', $html);
        $this->assertStringContains('Warning message', $html);
    }

    /** @test */
    public function it_can_render_alert_with_custom_options()
    {
        $alert = new \Wahyudedik\LaravelAlert\Models\Alert('info', 'Custom alert', 'Custom Title', [
            'class' => 'custom-class',
            'style' => 'color: red;',
            'icon' => 'fas fa-info',
            'dismissible' => false,
            'animation' => 'slide',
            'theme' => 'custom'
        ]);

        $view = View::make('laravel-alert::components.bootstrap.alert', [
            'alert' => $alert,
            'config' => config('laravel-alert')
        ]);

        $html = $view->render();

        $this->assertStringContains('custom-class', $html);
        $this->assertStringContains('color: red;', $html);
        $this->assertStringContains('fas fa-info', $html);
        $this->assertStringContains('Custom Title', $html);
        $this->assertStringContains('data-animation="slide"', $html);
        $this->assertStringContains('data-theme="custom"', $html);
    }

    /** @test */
    public function it_can_render_alert_with_html_content()
    {
        $alert = new \Wahyudedik\LaravelAlert\Models\Alert('success', 'HTML alert', null, [
            'html_content' => '<strong>Bold text</strong> and <em>italic text</em>'
        ]);

        $view = View::make('laravel-alert::components.bootstrap.alert', [
            'alert' => $alert,
            'config' => config('laravel-alert')
        ]);

        $html = $view->render();

        $this->assertStringContains('<strong>Bold text</strong>', $html);
        $this->assertStringContains('<em>italic text</em>', $html);
    }

    /** @test */
    public function it_can_render_alert_with_expiration()
    {
        $alert = new \Wahyudedik\LaravelAlert\Models\Alert('warning', 'Expiring alert', null, [
            'expires_at' => time() + 3600
        ]);

        $view = View::make('laravel-alert::components.bootstrap.alert', [
            'alert' => $alert,
            'config' => config('laravel-alert')
        ]);

        $html = $view->render();

        $this->assertStringContains('data-expires-at', $html);
        $this->assertStringContains('Expiring alert', $html);
    }

    /** @test */
    public function it_can_render_alert_with_auto_dismiss()
    {
        $alert = new \Wahyudedik\LaravelAlert\Models\Alert('info', 'Auto-dismiss alert', null, [
            'auto_dismiss_delay' => 3000
        ]);

        $view = View::make('laravel-alert::components.bootstrap.alert', [
            'alert' => $alert,
            'config' => config('laravel-alert')
        ]);

        $html = $view->render();

        $this->assertStringContains('data-auto-dismiss="true"', $html);
        $this->assertStringContains('data-dismiss-delay="3000"', $html);
        $this->assertStringContains('Auto-dismiss alert', $html);
    }

    /** @test */
    public function it_can_render_alert_with_data_attributes()
    {
        $alert = new \Wahyudedik\LaravelAlert\Models\Alert('error', 'Data attribute alert', null, [
            'data_attributes' => [
                'tracking' => 'error-123',
                'category' => 'validation',
                'priority' => 'high'
            ]
        ]);

        $view = View::make('laravel-alert::components.bootstrap.alert', [
            'alert' => $alert,
            'config' => config('laravel-alert')
        ]);

        $html = $view->render();

        $this->assertStringContains('data-tracking="error-123"', $html);
        $this->assertStringContains('data-category="validation"', $html);
        $this->assertStringContains('data-priority="high"', $html);
    }

    /** @test */
    public function it_can_render_tailwind_theme()
    {
        $alert = new \Wahyudedik\LaravelAlert\Models\Alert('success', 'Tailwind alert');

        $view = View::make('laravel-alert::components.tailwind.alert', [
            'alert' => $alert,
            'config' => config('laravel-alert')
        ]);

        $html = $view->render();

        $this->assertStringContains('rounded-md p-4', $html);
        $this->assertStringContains('Tailwind alert', $html);
        $this->assertStringContains('role="alert"', $html);
    }

    /** @test */
    public function it_can_render_bulma_theme()
    {
        $alert = new \Wahyudedik\LaravelAlert\Models\Alert('info', 'Bulma alert');

        $view = View::make('laravel-alert::components.bulma.alert', [
            'alert' => $alert,
            'config' => config('laravel-alert')
        ]);

        $html = $view->render();

        $this->assertStringContains('notification', $html);
        $this->assertStringContains('Bulma alert', $html);
        $this->assertStringContains('role="alert"', $html);
    }

    /** @test */
    public function it_can_render_alerts_with_positioning()
    {
        Alert::success('Positioned alert');

        $view = View::make('laravel-alert::components.bootstrap.alerts', [
            'alerts' => Alert::getAlerts(),
            'config' => array_merge(config('laravel-alert'), [
                'position' => 'bottom-left'
            ])
        ]);

        $html = $view->render();

        $this->assertStringContains('bottom: 20px', $html);
        $this->assertStringContains('left: 20px', $html);
    }

    /** @test */
    public function it_can_render_alerts_with_animation()
    {
        Alert::warning('Animated alert');

        $view = View::make('laravel-alert::components.bootstrap.alerts', [
            'alerts' => Alert::getAlerts(),
            'config' => array_merge(config('laravel-alert'), [
                'animation' => 'slide'
            ])
        ]);

        $html = $view->render();

        $this->assertStringContains('alert-slide', $html);
    }

    /** @test */
    public function it_can_render_alerts_with_custom_container()
    {
        Alert::info('Container alert');

        $view = View::make('laravel-alert::components.bootstrap.alerts', [
            'alerts' => Alert::getAlerts(),
            'config' => config('laravel-alert'),
            'containerClass' => 'custom-container',
            'containerStyle' => 'background: #f0f0f0;'
        ]);

        $html = $view->render();

        $this->assertStringContains('custom-container', $html);
        $this->assertStringContains('background: #f0f0f0;', $html);
    }
}
