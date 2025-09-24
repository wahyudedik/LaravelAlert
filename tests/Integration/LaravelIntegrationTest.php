<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Wahyudedik\LaravelAlert\Facades\Alert;
use Wahyudedik\LaravelAlert\Managers\AlertManager;
use Wahyudedik\LaravelAlert\View\Components\AlertComponent;
use Wahyudedik\LaravelAlert\View\Components\AlertsComponent;

class LaravelIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make('view')->addNamespace('laravel-alert', __DIR__ . '/../../resources/views');
    }

    /** @test */
    public function it_can_use_alert_facade()
    {
        Alert::success('Test message');

        $alerts = Alert::getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('success', $alerts[0]['type']);
        $this->assertEquals('Test message', $alerts[0]['message']);
    }

    /** @test */
    public function it_can_use_alert_facade_with_title()
    {
        Alert::success('Test message', 'Test Title');

        $alerts = Alert::getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('Test Title', $alerts[0]['title']);
    }

    /** @test */
    public function it_can_use_alert_facade_with_options()
    {
        Alert::success('Test message', 'Test Title', [
            'dismissible' => true,
            'auto_dismiss' => true,
            'auto_dismiss_delay' => 5000
        ]);

        $alerts = Alert::getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertTrue($alerts[0]['dismissible']);
        $this->assertTrue($alerts[0]['auto_dismiss']);
        $this->assertEquals(5000, $alerts[0]['auto_dismiss_delay']);
    }

    /** @test */
    public function it_can_use_alert_facade_fluent_api()
    {
        Alert::success('Test message')
            ->withTitle('Test Title')
            ->withIcon('fas fa-check')
            ->withClass('custom-alert')
            ->withStyle('border-left: 4px solid #28a745;')
            ->dismissible(true)
            ->autoDismiss(true)
            ->autoDismissDelay(5000);

        $alerts = Alert::getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('Test Title', $alerts[0]['title']);
        $this->assertEquals('fas fa-check', $alerts[0]['icon']);
        $this->assertEquals('custom-alert', $alerts[0]['class']);
        $this->assertEquals('border-left: 4px solid #28a745;', $alerts[0]['style']);
        $this->assertTrue($alerts[0]['dismissible']);
        $this->assertTrue($alerts[0]['auto_dismiss']);
        $this->assertEquals(5000, $alerts[0]['auto_dismiss_delay']);
    }

    /** @test */
    public function it_can_use_alert_facade_chain_methods()
    {
        Alert::success('Test message')
            ->withTitle('Test Title')
            ->withIcon('fas fa-check')
            ->withClass('custom-alert')
            ->withStyle('border-left: 4px solid #28a745;')
            ->dismissible(true)
            ->autoDismiss(true)
            ->autoDismissDelay(5000)
            ->withContext('test_context')
            ->withField('test_field')
            ->withForm('test_form')
            ->withPriority(5);

        $alerts = Alert::getAlerts();
        $this->assertCount(1, $alerts);
        $alert = $alerts[0];

        $this->assertEquals('Test Title', $alert['title']);
        $this->assertEquals('fas fa-check', $alert['icon']);
        $this->assertEquals('custom-alert', $alert['class']);
        $this->assertEquals('border-left: 4px solid #28a745;', $alert['style']);
        $this->assertTrue($alert['dismissible']);
        $this->assertTrue($alert['auto_dismiss']);
        $this->assertEquals(5000, $alert['auto_dismiss_delay']);
        $this->assertEquals('test_context', $alert['context']);
        $this->assertEquals('test_field', $alert['field']);
        $this->assertEquals('test_form', $alert['form']);
        $this->assertEquals(5, $alert['priority']);
    }

    /** @test */
    public function it_can_use_alert_facade_expiration_methods()
    {
        Alert::success('Test message')
            ->expiresIn(3600)
            ->temporary(300)
            ->flash(3000);

        $alerts = Alert::getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertNotNull($alerts[0]['expires_at']);
        $this->assertTrue($alerts[0]['auto_dismiss']);
        $this->assertEquals(3000, $alerts[0]['auto_dismiss_delay']);
    }

    /** @test */
    public function it_can_use_alert_facade_data_attributes()
    {
        Alert::success('Test message')
            ->withDataAttribute('data-custom', 'value')
            ->withDataAttribute('data-test', 'true')
            ->withDataAttributes([
                'data-extra' => 'extra_value',
                'data-number' => 123
            ]);

        $alerts = Alert::getAlerts();
        $this->assertCount(1, $alerts);
        $dataAttributes = $alerts[0]['data_attributes'];
        $this->assertEquals('value', $dataAttributes['data-custom']);
        $this->assertEquals('true', $dataAttributes['data-test']);
        $this->assertEquals('extra_value', $dataAttributes['data-extra']);
        $this->assertEquals(123, $dataAttributes['data-number']);
    }

    /** @test */
    public function it_can_use_alert_facade_html_content()
    {
        Alert::success('Test message')
            ->withHtmlContent('<strong>Bold text</strong> and <em>italic text</em>');

        $alerts = Alert::getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('<strong>Bold text</strong> and <em>italic text</em>', $alerts[0]['html_content']);
    }

    /** @test */
    public function it_can_use_alert_facade_theme_methods()
    {
        Alert::success('Test message')
            ->withTheme('bootstrap')
            ->withPosition('top-right')
            ->withAnimation('fade');

        $alerts = Alert::getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('bootstrap', $alerts[0]['theme']);
        $this->assertEquals('top-right', $alerts[0]['position']);
        $this->assertEquals('fade', $alerts[0]['animation']);
    }

    /** @test */
    public function it_can_use_alert_facade_alert_type_methods()
    {
        Alert::success('Test message')
            ->asToast()
            ->asModal()
            ->asInline();

        $alerts = Alert::getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('inline', $alerts[0]['alert_type']);
    }

    /** @test */
    public function it_can_use_alert_facade_priority_methods()
    {
        Alert::success('Test message')
            ->withPriority(5)
            ->highPriority()
            ->lowPriority();

        $alerts = Alert::getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals(1, $alerts[0]['priority']);
    }

    /** @test */
    public function it_can_use_alert_facade_utility_methods()
    {
        Alert::success('First alert');
        Alert::error('Second alert');

        $this->assertTrue(Alert::hasAlerts());
        $this->assertEquals(2, Alert::count());

        $firstAlert = Alert::first();
        $this->assertEquals('success', $firstAlert['type']);

        $lastAlert = Alert::last();
        $this->assertEquals('error', $lastAlert['type']);

        $successAlerts = Alert::getAlertsByType('success');
        $this->assertCount(1, $successAlerts);

        Alert::clearByType('success');
        $this->assertEquals(1, Alert::count());

        Alert::clear();
        $this->assertFalse(Alert::hasAlerts());
    }

    /** @test */
    public function it_can_use_alert_facade_bulk_methods()
    {
        $alerts = [
            ['type' => 'success', 'message' => 'First alert'],
            ['type' => 'error', 'message' => 'Second alert'],
            ['type' => 'warning', 'message' => 'Third alert']
        ];

        Alert::addMultiple($alerts);

        $this->assertEquals(3, Alert::count());

        $flushedAlerts = Alert::flush();
        $this->assertCount(3, $flushedAlerts);
        $this->assertEquals(0, Alert::count());
    }

    /** @test */
    public function it_can_use_alert_facade_expiration_methods()
    {
        Alert::addWithExpiration('info', 'Test message', null, 3600);
        Alert::addWithAutoDismiss('info', 'Test message', null, 5000);
        Alert::temporary('info', 'Test message', null, 300);
        Alert::flash('info', 'Test message', null, 3000);

        $alerts = Alert::getAlerts();
        $this->assertCount(4, $alerts);

        $expiredAlerts = Alert::getExpiredAlerts();
        $this->assertCount(0, $expiredAlerts);

        $autoDismissAlerts = Alert::getAutoDismissAlerts();
        $this->assertCount(2, $autoDismissAlerts);
    }

    /** @test */
    public function it_can_use_alert_facade_cleanup_methods()
    {
        Alert::addWithExpiration('info', 'Test message', null, -3600);
        Alert::success('Valid alert');

        $this->assertEquals(2, Alert::count());

        Alert::cleanupExpired();

        $this->assertEquals(1, Alert::count());
    }

    /** @test */
    public function it_can_use_alert_facade_rendering_methods()
    {
        Alert::success('Test message');

        $html = Alert::renderAll();
        $this->assertStringContainsString('Test message', $html);
        $this->assertStringContainsString('alert-success', $html);
    }

    /** @test */
    public function it_can_use_alert_facade_with_session()
    {
        Session::start();

        Alert::success('Test message');

        $this->assertTrue(Session::has('laravel_alerts'));
        $this->assertCount(1, Session::get('laravel_alerts'));
    }

    /** @test */
    public function it_can_use_alert_facade_with_multiple_sessions()
    {
        Session::start();

        Alert::success('First alert');
        Alert::error('Second alert');
        Alert::warning('Third alert');

        $this->assertCount(3, Alert::getAlerts());
        $this->assertEquals(3, Alert::count());
        $this->assertTrue(Alert::hasAlerts());
    }

    /** @test */
    public function it_can_use_alert_facade_with_complex_scenarios()
    {
        // Create multiple alerts with different types
        Alert::success('Success message', 'Success Title')
            ->withIcon('fas fa-check')
            ->withClass('custom-success')
            ->dismissible(true)
            ->autoDismiss(true)
            ->autoDismissDelay(5000);

        Alert::error('Error message', 'Error Title')
            ->withIcon('fas fa-times')
            ->withClass('custom-error')
            ->dismissible(true)
            ->autoDismiss(false);

        Alert::warning('Warning message', 'Warning Title')
            ->withIcon('fas fa-exclamation')
            ->withClass('custom-warning')
            ->dismissible(true)
            ->autoDismiss(true)
            ->autoDismissDelay(3000);

        Alert::info('Info message', 'Info Title')
            ->withIcon('fas fa-info')
            ->withClass('custom-info')
            ->dismissible(true)
            ->autoDismiss(true)
            ->autoDismissDelay(7000);

        // Verify all alerts were created
        $this->assertEquals(4, Alert::count());
        $this->assertTrue(Alert::hasAlerts());

        // Verify alert types
        $successAlerts = Alert::getAlertsByType('success');
        $this->assertCount(1, $successAlerts);

        $errorAlerts = Alert::getAlertsByType('error');
        $this->assertCount(1, $errorAlerts);

        $warningAlerts = Alert::getAlertsByType('warning');
        $this->assertCount(1, $warningAlerts);

        $infoAlerts = Alert::getAlertsByType('info');
        $this->assertCount(1, $infoAlerts);

        // Verify first and last alerts
        $firstAlert = Alert::first();
        $this->assertEquals('success', $firstAlert['type']);
        $this->assertEquals('Success Title', $firstAlert['title']);
        $this->assertEquals('fas fa-check', $firstAlert['icon']);
        $this->assertEquals('custom-success', $firstAlert['class']);
        $this->assertTrue($firstAlert['dismissible']);
        $this->assertTrue($firstAlert['auto_dismiss']);
        $this->assertEquals(5000, $firstAlert['auto_dismiss_delay']);

        $lastAlert = Alert::last();
        $this->assertEquals('info', $lastAlert['type']);
        $this->assertEquals('Info Title', $lastAlert['title']);
        $this->assertEquals('fas fa-info', $lastAlert['icon']);
        $this->assertEquals('custom-info', $lastAlert['class']);
        $this->assertTrue($lastAlert['dismissible']);
        $this->assertTrue($lastAlert['auto_dismiss']);
        $this->assertEquals(7000, $lastAlert['auto_dismiss_delay']);

        // Test clearing by type
        Alert::clearByType('success');
        $this->assertEquals(3, Alert::count());

        Alert::clearByType('error');
        $this->assertEquals(2, Alert::count());

        // Test clearing all
        Alert::clear();
        $this->assertEquals(0, Alert::count());
        $this->assertFalse(Alert::hasAlerts());
    }

    /** @test */
    public function it_can_use_alert_facade_with_edge_cases()
    {
        // Test with empty message
        Alert::success('');
        $this->assertEquals(1, Alert::count());

        // Test with null title
        Alert::success('Test message', null);
        $this->assertEquals(2, Alert::count());

        // Test with empty options
        Alert::success('Test message', 'Test Title', []);
        $this->assertEquals(3, Alert::count());

        // Test with null options
        Alert::success('Test message', 'Test Title', null);
        $this->assertEquals(4, Alert::count());

        // Test with invalid type
        Alert::add('invalid_type', 'Test message');
        $this->assertEquals(5, Alert::count());

        // Test with special characters
        Alert::success('Test message with special chars: <>&"\'');
        $this->assertEquals(6, Alert::count());

        // Test with unicode characters
        Alert::success('Test message with unicode: 测试 🚀 émojis');
        $this->assertEquals(7, Alert::count());

        // Test with long message
        $longMessage = str_repeat('This is a very long message. ', 100);
        Alert::success($longMessage);
        $this->assertEquals(8, Alert::count());

        // Test with complex options
        $complexOptions = [
            'dismissible' => true,
            'auto_dismiss' => true,
            'auto_dismiss_delay' => 5000,
            'theme' => 'bootstrap',
            'position' => 'top-right',
            'animation' => 'fade',
            'icon' => 'fas fa-check',
            'class' => 'custom-alert',
            'style' => 'border-left: 4px solid #28a745;',
            'data_attributes' => [
                'data-custom' => 'value',
                'data-test' => 'true'
            ],
            'context' => 'test_context',
            'field' => 'test_field',
            'form' => 'test_form',
            'priority' => 5,
            'html_content' => '<strong>Bold text</strong>'
        ];

        Alert::success('Test message', 'Test Title', $complexOptions);
        $this->assertEquals(9, Alert::count());

        // Verify all alerts were created
        $this->assertTrue(Alert::hasAlerts());
        $this->assertEquals(9, Alert::count());
    }
}
