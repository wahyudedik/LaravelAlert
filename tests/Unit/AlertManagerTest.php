<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Wahyudedik\LaravelAlert\Managers\AlertManager;
use Wahyudedik\LaravelAlert\Models\Alert;

class AlertManagerTest extends TestCase
{
    use RefreshDatabase;

    protected AlertManager $alertManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->alertManager = new AlertManager();
    }

    /** @test */
    public function it_can_create_success_alert()
    {
        $this->alertManager->success('Test success message');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('success', $alerts[0]['type']);
        $this->assertEquals('Test success message', $alerts[0]['message']);
    }

    /** @test */
    public function it_can_create_error_alert()
    {
        $this->alertManager->error('Test error message');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('error', $alerts[0]['type']);
        $this->assertEquals('Test error message', $alerts[0]['message']);
    }

    /** @test */
    public function it_can_create_warning_alert()
    {
        $this->alertManager->warning('Test warning message');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('warning', $alerts[0]['type']);
        $this->assertEquals('Test warning message', $alerts[0]['message']);
    }

    /** @test */
    public function it_can_create_info_alert()
    {
        $this->alertManager->info('Test info message');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('info', $alerts[0]['type']);
        $this->assertEquals('Test info message', $alerts[0]['message']);
    }

    /** @test */
    public function it_can_create_alert_with_title()
    {
        $this->alertManager->success('Test message', 'Test Title');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('Test Title', $alerts[0]['title']);
    }

    /** @test */
    public function it_can_create_alert_with_options()
    {
        $options = [
            'dismissible' => true,
            'auto_dismiss' => true,
            'auto_dismiss_delay' => 5000,
            'theme' => 'bootstrap',
            'position' => 'top-right',
            'animation' => 'fade'
        ];

        $this->alertManager->success('Test message', 'Test Title', $options);

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertTrue($alerts[0]['dismissible']);
        $this->assertTrue($alerts[0]['auto_dismiss']);
        $this->assertEquals(5000, $alerts[0]['auto_dismiss_delay']);
    }

    /** @test */
    public function it_can_create_multiple_alerts()
    {
        $this->alertManager->success('First alert');
        $this->alertManager->error('Second alert');
        $this->alertManager->warning('Third alert');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(3, $alerts);
    }

    /** @test */
    public function it_can_clear_all_alerts()
    {
        $this->alertManager->success('First alert');
        $this->alertManager->error('Second alert');

        $this->alertManager->clear();

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(0, $alerts);
    }

    /** @test */
    public function it_can_get_alerts_count()
    {
        $this->alertManager->success('First alert');
        $this->alertManager->error('Second alert');

        $this->assertEquals(2, $this->alertManager->count());
    }

    /** @test */
    public function it_can_check_if_has_alerts()
    {
        $this->assertFalse($this->alertManager->hasAlerts());

        $this->alertManager->success('Test alert');

        $this->assertTrue($this->alertManager->hasAlerts());
    }

    /** @test */
    public function it_can_get_alerts_by_type()
    {
        $this->alertManager->success('Success alert 1');
        $this->alertManager->success('Success alert 2');
        $this->alertManager->error('Error alert');

        $successAlerts = $this->alertManager->getAlertsByType('success');
        $this->assertCount(2, $successAlerts);

        $errorAlerts = $this->alertManager->getAlertsByType('error');
        $this->assertCount(1, $errorAlerts);
    }

    /** @test */
    public function it_can_clear_alerts_by_type()
    {
        $this->alertManager->success('Success alert 1');
        $this->alertManager->success('Success alert 2');
        $this->alertManager->error('Error alert');

        $this->alertManager->clearByType('success');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('error', $alerts[0]['type']);
    }

    /** @test */
    public function it_can_add_multiple_alerts()
    {
        $alerts = [
            ['type' => 'success', 'message' => 'First alert'],
            ['type' => 'error', 'message' => 'Second alert'],
            ['type' => 'warning', 'message' => 'Third alert']
        ];

        $this->alertManager->addMultiple($alerts);

        $storedAlerts = $this->alertManager->getAlerts();
        $this->assertCount(3, $storedAlerts);
    }

    /** @test */
    public function it_can_get_first_alert()
    {
        $this->alertManager->success('First alert');
        $this->alertManager->error('Second alert');

        $firstAlert = $this->alertManager->first();
        $this->assertEquals('success', $firstAlert['type']);
        $this->assertEquals('First alert', $firstAlert['message']);
    }

    /** @test */
    public function it_can_get_last_alert()
    {
        $this->alertManager->success('First alert');
        $this->alertManager->error('Second alert');

        $lastAlert = $this->alertManager->last();
        $this->assertEquals('error', $lastAlert['type']);
        $this->assertEquals('Second alert', $lastAlert['message']);
    }

    /** @test */
    public function it_can_remove_alert_by_id()
    {
        $this->alertManager->success('Test alert');
        $alerts = $this->alertManager->getAlerts();
        $alertId = $alerts[0]['id'];

        $this->alertManager->removeById($alertId);

        $this->assertCount(0, $this->alertManager->getAlerts());
    }

    /** @test */
    public function it_can_flush_alerts()
    {
        $this->alertManager->success('First alert');
        $this->alertManager->error('Second alert');

        $flushedAlerts = $this->alertManager->flush();

        $this->assertCount(2, $flushedAlerts);
        $this->assertCount(0, $this->alertManager->getAlerts());
    }

    /** @test */
    public function it_can_create_alert_with_expiration()
    {
        $this->alertManager->addWithExpiration('info', 'Test message', null, 3600);

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertNotNull($alerts[0]['expires_at']);
    }

    /** @test */
    public function it_can_create_alert_with_auto_dismiss()
    {
        $this->alertManager->addWithAutoDismiss('info', 'Test message', null, 5000);

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertTrue($alerts[0]['auto_dismiss']);
        $this->assertEquals(5000, $alerts[0]['auto_dismiss_delay']);
    }

    /** @test */
    public function it_can_create_temporary_alert()
    {
        $this->alertManager->temporary('info', 'Test message', null, 300);

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertNotNull($alerts[0]['expires_at']);
    }

    /** @test */
    public function it_can_create_flash_alert()
    {
        $this->alertManager->flash('info', 'Test message', null, 3000);

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertTrue($alerts[0]['auto_dismiss']);
        $this->assertEquals(3000, $alerts[0]['auto_dismiss_delay']);
    }

    /** @test */
    public function it_can_cleanup_expired_alerts()
    {
        // Create alert with past expiration
        $this->alertManager->addWithExpiration('info', 'Test message', null, -3600);

        $this->alertManager->cleanupExpired();

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(0, $alerts);
    }

    /** @test */
    public function it_can_get_expired_alerts()
    {
        $this->alertManager->addWithExpiration('info', 'Test message', null, -3600);

        $expiredAlerts = $this->alertManager->getExpiredAlerts();
        $this->assertCount(1, $expiredAlerts);
    }

    /** @test */
    public function it_can_get_auto_dismiss_alerts()
    {
        $this->alertManager->addWithAutoDismiss('info', 'Test message', null, 5000);

        $autoDismissAlerts = $this->alertManager->getAutoDismissAlerts();
        $this->assertCount(1, $autoDismissAlerts);
    }

    /** @test */
    public function it_can_render_single_alert()
    {
        $html = $this->alertManager->render('success', 'Test message');

        $this->assertStringContainsString('Test message', $html);
        $this->assertStringContainsString('alert-success', $html);
    }

    /** @test */
    public function it_can_render_all_alerts()
    {
        $this->alertManager->success('First alert');
        $this->alertManager->error('Second alert');

        $html = $this->alertManager->renderAll();

        $this->assertStringContainsString('First alert', $html);
        $this->assertStringContainsString('Second alert', $html);
    }

    /** @test */
    public function it_can_handle_empty_alerts()
    {
        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(0, $alerts);

        $this->assertFalse($this->alertManager->hasAlerts());
        $this->assertEquals(0, $this->alertManager->count());
    }

    /** @test */
    public function it_can_handle_invalid_alert_data()
    {
        $this->alertManager->add('invalid_type', 'Test message');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('invalid_type', $alerts[0]['type']);
    }

    /** @test */
    public function it_can_handle_large_number_of_alerts()
    {
        for ($i = 0; $i < 100; $i++) {
            $this->alertManager->success("Alert {$i}");
        }

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(100, $alerts);
    }

    /** @test */
    public function it_can_handle_alert_with_special_characters()
    {
        $message = 'Test alert with special chars: <>&"\'';
        $this->alertManager->success($message);

        $alerts = $this->alertManager->getAlerts();
        $this->assertEquals($message, $alerts[0]['message']);
    }

    /** @test */
    public function it_can_handle_alert_with_unicode_characters()
    {
        $message = 'Test alert with unicode: 测试 🚀 émojis';
        $this->alertManager->success($message);

        $alerts = $this->alertManager->getAlerts();
        $this->assertEquals($message, $alerts[0]['message']);
    }

    /** @test */
    public function it_can_handle_alert_with_long_message()
    {
        $longMessage = str_repeat('This is a very long message. ', 100);
        $this->alertManager->success($longMessage);

        $alerts = $this->alertManager->getAlerts();
        $this->assertEquals($longMessage, $alerts[0]['message']);
    }

    /** @test */
    public function it_can_handle_alert_with_complex_options()
    {
        $options = [
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
            'priority' => 5
        ];

        $this->alertManager->success('Test message', 'Test Title', $options);

        $alerts = $this->alertManager->getAlerts();
        $alert = $alerts[0];

        $this->assertTrue($alert['dismissible']);
        $this->assertTrue($alert['auto_dismiss']);
        $this->assertEquals(5000, $alert['auto_dismiss_delay']);
        $this->assertEquals('bootstrap', $alert['theme']);
        $this->assertEquals('top-right', $alert['position']);
        $this->assertEquals('fade', $alert['animation']);
        $this->assertEquals('fas fa-check', $alert['icon']);
        $this->assertEquals('custom-alert', $alert['class']);
        $this->assertEquals('border-left: 4px solid #28a745;', $alert['style']);
        $this->assertEquals('test_context', $alert['context']);
        $this->assertEquals('test_field', $alert['field']);
        $this->assertEquals('test_form', $alert['form']);
        $this->assertEquals(5, $alert['priority']);
    }
}
