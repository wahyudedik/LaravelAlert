<?php

namespace Tests\Browser;

use Laravel\Dusk\TestCase as DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Wahyudedik\LaravelAlert\Facades\Alert;

class JavaScriptFeaturesTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /** @test */
    public function it_can_display_alerts_with_javascript()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alerts')
                ->assertSee('Test Alert')
                ->assertSee('alert-success')
                ->assertSee('alert-dismissible');
        });
    }

    /** @test */
    public function it_can_auto_dismiss_alerts()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-auto-dismiss')
                ->assertSee('Auto Dismiss Alert')
                ->waitForText('Auto Dismiss Alert', 1)
                ->pause(6000) // Wait for auto-dismiss
                ->assertDontSee('Auto Dismiss Alert');
        });
    }

    /** @test */
    public function it_can_manually_dismiss_alerts()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-manual-dismiss')
                ->assertSee('Manual Dismiss Alert')
                ->click('.alert-dismissible .btn-close')
                ->assertDontSee('Manual Dismiss Alert');
        });
    }

    /** @test */
    public function it_can_handle_multiple_alerts()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-multiple-alerts')
                ->assertSee('First Alert')
                ->assertSee('Second Alert')
                ->assertSee('Third Alert')
                ->assertSee('alert-success')
                ->assertSee('alert-error')
                ->assertSee('alert-warning');
        });
    }

    /** @test */
    public function it_can_handle_alert_animations()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-animations')
                ->assertSee('Fade Alert')
                ->assertSee('Slide Alert')
                ->assertSee('Bounce Alert')
                ->assertSee('Scale Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_positions()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-positions')
                ->assertSee('Top Right Alert')
                ->assertSee('Top Left Alert')
                ->assertSee('Bottom Right Alert')
                ->assertSee('Bottom Left Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_themes()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-themes')
                ->assertSee('Bootstrap Alert')
                ->assertSee('Tailwind Alert')
                ->assertSee('Bulma Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_icons()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-icons')
                ->assertSee('fas fa-check')
                ->assertSee('fas fa-times')
                ->assertSee('fas fa-exclamation')
                ->assertSee('fas fa-info');
        });
    }

    /** @test */
    public function it_can_handle_alert_priorities()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-priorities')
                ->assertSee('High Priority Alert')
                ->assertSee('Normal Priority Alert')
                ->assertSee('Low Priority Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_contexts()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-contexts')
                ->assertSee('User Registration Alert')
                ->assertSee('Form Validation Alert')
                ->assertSee('System Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_forms()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-forms')
                ->assertSee('Login Form Alert')
                ->assertSee('Registration Form Alert')
                ->assertSee('Contact Form Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_fields()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-fields')
                ->assertSee('Email Field Alert')
                ->assertSee('Password Field Alert')
                ->assertSee('Name Field Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_html_content()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-html')
                ->assertSee('Bold text')
                ->assertSee('Italic text')
                ->assertSee('Underlined text');
        });
    }

    /** @test */
    public function it_can_handle_alert_data_attributes()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-data')
                ->assertAttribute('.alert[data-custom]', 'data-custom', 'value')
                ->assertAttribute('.alert[data-test]', 'data-test', 'true')
                ->assertAttribute('.alert[data-number]', 'data-number', '123');
        });
    }

    /** @test */
    public function it_can_handle_alert_custom_styles()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-styles')
                ->assertSee('Custom Style Alert')
                ->assertAttribute('.alert[style]', 'style', 'border-left: 4px solid #28a745;');
        });
    }

    /** @test */
    public function it_can_handle_alert_custom_classes()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-classes')
                ->assertSee('Custom Class Alert')
                ->assertClass('.alert', 'custom-alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_expiration()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-expiration')
                ->assertSee('Expiring Alert')
                ->pause(2000) // Wait for expiration
                ->assertDontSee('Expiring Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_cleanup()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-cleanup')
                ->assertSee('Cleanup Alert')
                ->pause(1000)
                ->assertDontSee('Cleanup Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_ajax()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-ajax')
                ->click('#create-alert-btn')
                ->waitForText('AJAX Alert', 5)
                ->assertSee('AJAX Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_websocket()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-websocket')
                ->click('#connect-websocket-btn')
                ->waitForText('WebSocket Alert', 5)
                ->assertSee('WebSocket Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_pusher()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-pusher')
                ->click('#connect-pusher-btn')
                ->waitForText('Pusher Alert', 5)
                ->assertSee('Pusher Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_email()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-email')
                ->click('#send-email-btn')
                ->waitForText('Email Alert', 5)
                ->assertSee('Email Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_bulk_operations()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-bulk')
                ->click('#create-bulk-alerts-btn')
                ->waitForText('Bulk Alert 1', 5)
                ->assertSee('Bulk Alert 1')
                ->assertSee('Bulk Alert 2')
                ->assertSee('Bulk Alert 3');
        });
    }

    /** @test */
    public function it_can_handle_alert_filtering()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-filtering')
                ->select('#alert-type-filter', 'success')
                ->assertSee('Success Alert')
                ->assertDontSee('Error Alert')
                ->assertDontSee('Warning Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_searching()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-search')
                ->type('#alert-search', 'test')
                ->assertSee('Test Alert')
                ->assertDontSee('Other Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_sorting()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-sorting')
                ->click('#sort-by-priority')
                ->assertSee('High Priority Alert')
                ->assertSee('Normal Priority Alert')
                ->assertSee('Low Priority Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_pagination()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-pagination')
                ->assertSee('Alert 1')
                ->assertSee('Alert 2')
                ->assertSee('Alert 3')
                ->click('#next-page')
                ->assertSee('Alert 4')
                ->assertSee('Alert 5')
                ->assertSee('Alert 6');
        });
    }

    /** @test */
    public function it_can_handle_alert_statistics()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-statistics')
                ->assertSee('Total Alerts: 10')
                ->assertSee('Success: 4')
                ->assertSee('Error: 3')
                ->assertSee('Warning: 2')
                ->assertSee('Info: 1');
        });
    }

    /** @test */
    public function it_can_handle_alert_export()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-export')
                ->click('#export-alerts-btn')
                ->waitForText('Export completed', 5)
                ->assertSee('Export completed');
        });
    }

    /** @test */
    public function it_can_handle_alert_import()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-import')
                ->attach('#import-file', __DIR__ . '/../fixtures/alerts.json')
                ->click('#import-alerts-btn')
                ->waitForText('Import completed', 5)
                ->assertSee('Import completed');
        });
    }

    /** @test */
    public function it_can_handle_alert_backup()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-backup')
                ->click('#backup-alerts-btn')
                ->waitForText('Backup completed', 5)
                ->assertSee('Backup completed');
        });
    }

    /** @test */
    public function it_can_handle_alert_restore()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-restore')
                ->click('#restore-alerts-btn')
                ->waitForText('Restore completed', 5)
                ->assertSee('Restore completed');
        });
    }

    /** @test */
    public function it_can_handle_alert_scheduling()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-scheduling')
                ->click('#schedule-alert-btn')
                ->waitForText('Alert scheduled', 5)
                ->assertSee('Alert scheduled');
        });
    }

    /** @test */
    public function it_can_handle_alert_templates()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-templates')
                ->assertSee('Template 1')
                ->assertSee('Template 2')
                ->assertSee('Template 3')
                ->click('#use-template-1')
                ->assertSee('Template 1 Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_workflows()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-workflows')
                ->click('#start-workflow-btn')
                ->waitForText('Workflow started', 5)
                ->assertSee('Workflow started')
                ->click('#next-step-btn')
                ->waitForText('Step 2', 5)
                ->assertSee('Step 2')
                ->click('#complete-workflow-btn')
                ->waitForText('Workflow completed', 5)
                ->assertSee('Workflow completed');
        });
    }

    /** @test */
    public function it_can_handle_alert_notifications()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-notifications')
                ->click('#enable-notifications-btn')
                ->waitForText('Notifications enabled', 5)
                ->assertSee('Notifications enabled')
                ->click('#test-notification-btn')
                ->waitForText('Test notification sent', 5)
                ->assertSee('Test notification sent');
        });
    }

    /** @test */
    public function it_can_handle_alert_analytics()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-analytics')
                ->assertSee('Alert Analytics')
                ->assertSee('Total Views: 100')
                ->assertSee('Total Clicks: 50')
                ->assertSee('Total Dismissals: 25')
                ->assertSee('Conversion Rate: 50%');
        });
    }

    /** @test */
    public function it_can_handle_alert_performance()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-performance')
                ->assertSee('Performance Metrics')
                ->assertSee('Load Time: 100ms')
                ->assertSee('Render Time: 50ms')
                ->assertSee('Memory Usage: 10MB')
                ->assertSee('CPU Usage: 5%');
        });
    }

    /** @test */
    public function it_can_handle_alert_security()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-security')
                ->assertSee('Security Features')
                ->assertSee('XSS Protection: Enabled')
                ->assertSee('CSRF Protection: Enabled')
                ->assertSee('Rate Limiting: Enabled')
                ->assertSee('Input Validation: Enabled');
        });
    }

    /** @test */
    public function it_can_handle_alert_accessibility()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-accessibility')
                ->assertSee('Accessibility Features')
                ->assertSee('ARIA Labels: Enabled')
                ->assertSee('Keyboard Navigation: Enabled')
                ->assertSee('Screen Reader Support: Enabled')
                ->assertSee('High Contrast Mode: Enabled');
        });
    }

    /** @test */
    public function it_can_handle_alert_responsive()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-responsive')
                ->assertSee('Responsive Design')
                ->resize(375, 667) // Mobile size
                ->assertSee('Mobile Alert')
                ->resize(768, 1024) // Tablet size
                ->assertSee('Tablet Alert')
                ->resize(1920, 1080) // Desktop size
                ->assertSee('Desktop Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_internationalization()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-i18n')
                ->assertSee('Internationalization')
                ->assertSee('English Alert')
                ->click('#switch-language-es')
                ->assertSee('Spanish Alert')
                ->click('#switch-language-fr')
                ->assertSee('French Alert')
                ->click('#switch-language-de')
                ->assertSee('German Alert');
        });
    }

    /** @test */
    public function it_can_handle_alert_customization()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-customization')
                ->assertSee('Customization Options')
                ->assertSee('Custom Theme')
                ->assertSee('Custom Colors')
                ->assertSee('Custom Fonts')
                ->assertSee('Custom Layout');
        });
    }

    /** @test */
    public function it_can_handle_alert_integration()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/test-alert-integration')
                ->assertSee('Integration Features')
                ->assertSee('API Integration')
                ->assertSee('Database Integration')
                ->assertSee('Cache Integration')
                ->assertSee('Queue Integration');
        });
    }
}
