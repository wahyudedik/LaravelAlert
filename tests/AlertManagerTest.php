<?php

namespace Wahyudedik\LaravelAlert\Tests;

use Orchestra\Testbench\TestCase;
use Wahyudedik\LaravelAlert\Managers\AlertManager;
use Wahyudedik\LaravelAlert\AlertServiceProvider;
use Illuminate\Session\Store;
use Illuminate\Session\SessionManager;

class AlertManagerTest extends TestCase
{
    protected AlertManager $alertManager;
    protected Store $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->session = $this->app->make('session');
        $this->alertManager = new AlertManager($this->session);
    }

    protected function getPackageProviders($app)
    {
        return [AlertServiceProvider::class];
    }

    /** @test */
    public function it_can_add_success_alert()
    {
        $this->alertManager->success('Operation completed successfully!');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('success', $alerts[0]->getType());
        $this->assertEquals('Operation completed successfully!', $alerts[0]->getMessage());
    }

    /** @test */
    public function it_can_add_error_alert()
    {
        $this->alertManager->error('Something went wrong!');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('error', $alerts[0]->getType());
        $this->assertEquals('Something went wrong!', $alerts[0]->getMessage());
    }

    /** @test */
    public function it_can_add_warning_alert()
    {
        $this->alertManager->warning('Please check your input.');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('warning', $alerts[0]->getType());
        $this->assertEquals('Please check your input.', $alerts[0]->getMessage());
    }

    /** @test */
    public function it_can_add_info_alert()
    {
        $this->alertManager->info('Welcome to our application!');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('info', $alerts[0]->getType());
        $this->assertEquals('Welcome to our application!', $alerts[0]->getMessage());
    }

    /** @test */
    public function it_can_add_multiple_alerts()
    {
        $this->alertManager->success('Success message');
        $this->alertManager->error('Error message');
        $this->alertManager->warning('Warning message');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(3, $alerts);
    }

    /** @test */
    public function it_can_clear_all_alerts()
    {
        $this->alertManager->success('Success message');
        $this->alertManager->error('Error message');

        $this->assertCount(2, $this->alertManager->getAlerts());

        $this->alertManager->clear();

        $this->assertCount(0, $this->alertManager->getAlerts());
    }

    /** @test */
    public function it_can_get_alerts_by_type()
    {
        $this->alertManager->success('Success 1');
        $this->alertManager->success('Success 2');
        $this->alertManager->error('Error 1');

        $successAlerts = $this->alertManager->getAlertsByType('success');
        $errorAlerts = $this->alertManager->getAlertsByType('error');

        $this->assertCount(2, $successAlerts);
        $this->assertCount(1, $errorAlerts);
    }

    /** @test */
    public function it_can_clear_alerts_by_type()
    {
        $this->alertManager->success('Success 1');
        $this->alertManager->error('Error 1');
        $this->alertManager->success('Success 2');

        $this->alertManager->clearByType('success');

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('error', $alerts[0]->getType());
    }

    /** @test */
    public function it_can_add_multiple_alerts_at_once()
    {
        $alerts = [
            ['type' => 'success', 'message' => 'Success 1'],
            ['type' => 'error', 'message' => 'Error 1', 'title' => 'Error Title'],
            ['type' => 'warning', 'message' => 'Warning 1', 'options' => ['dismissible' => false]],
        ];

        $this->alertManager->addMultiple($alerts);

        $storedAlerts = $this->alertManager->getAlerts();
        $this->assertCount(3, $storedAlerts);
    }

    /** @test */
    public function it_can_get_first_and_last_alerts()
    {
        $this->alertManager->success('First alert');
        $this->alertManager->error('Second alert');
        $this->alertManager->warning('Last alert');

        $first = $this->alertManager->first();
        $last = $this->alertManager->last();

        $this->assertEquals('success', $first->getType());
        $this->assertEquals('warning', $last->getType());
    }

    /** @test */
    public function it_can_count_alerts()
    {
        $this->assertEquals(0, $this->alertManager->count());

        $this->alertManager->success('Alert 1');
        $this->assertEquals(1, $this->alertManager->count());

        $this->alertManager->error('Alert 2');
        $this->assertEquals(2, $this->alertManager->count());
    }

    /** @test */
    public function it_can_check_if_has_alerts()
    {
        $this->assertFalse($this->alertManager->hasAlerts());

        $this->alertManager->success('Alert 1');
        $this->assertTrue($this->alertManager->hasAlerts());
    }

    /** @test */
    public function it_can_flush_alerts()
    {
        $this->alertManager->success('Alert 1');
        $this->alertManager->error('Alert 2');

        $flushedAlerts = $this->alertManager->flush();

        $this->assertCount(2, $flushedAlerts);
        $this->assertCount(0, $this->alertManager->getAlerts());
    }

    /** @test */
    public function it_respects_max_alerts_limit()
    {
        // Set max alerts to 3
        config(['laravel-alert.max_alerts' => 3]);

        $this->alertManager = new AlertManager($this->session);

        $this->alertManager->success('Alert 1');
        $this->alertManager->success('Alert 2');
        $this->alertManager->success('Alert 3');
        $this->alertManager->success('Alert 4'); // This should remove Alert 1

        $alerts = $this->alertManager->getAlerts();
        $this->assertCount(3, $alerts);
        $this->assertEquals('Alert 2', $alerts[0]->getMessage());
        $this->assertEquals('Alert 4', $alerts[2]->getMessage());
    }
}
