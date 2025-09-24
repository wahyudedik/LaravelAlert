<?php

/**
 * Complete Usage Examples for Laravel Alert
 * 
 * This file demonstrates all the features and capabilities
 * of the Laravel Alert library with practical examples.
 */

use Wahyudedik\LaravelAlert\Facades\Alert;
use Wahyudedik\LaravelAlert\Facades\Toast;
use Wahyudedik\LaravelAlert\Facades\Modal;
use Wahyudedik\LaravelAlert\Facades\Inline;

class CompleteUsageExamples
{
    /**
     * Basic Alert Examples
     */
    public function basicAlerts()
    {
        // Simple alerts
        Alert::success('Operation completed successfully!');
        Alert::error('Something went wrong!');
        Alert::warning('Please check your input!');
        Alert::info('New features are available!');

        // Alerts with titles
        Alert::success('User created successfully!', 'Success');
        Alert::error('Failed to save data!', 'Error');
        Alert::warning('Low disk space!', 'Warning');
        Alert::info('System maintenance scheduled!', 'Info');
    }

    /**
     * Advanced Alert Examples
     */
    public function advancedAlerts()
    {
        // Alert with custom options
        Alert::success('Data saved successfully!', 'Success', [
            'dismissible' => true,
            'auto_dismiss' => true,
            'auto_dismiss_delay' => 5000,
            'theme' => 'bootstrap',
            'position' => 'top-right',
            'animation' => 'fade',
            'icon' => 'fas fa-check',
            'class' => 'custom-alert',
            'style' => 'border-left: 4px solid #28a745;',
            'context' => 'user_registration',
            'field' => 'email',
            'form' => 'registration_form',
            'priority' => 5,
            'html_content' => '<strong>Bold text</strong> and <em>italic text</em>',
            'data_attributes' => [
                'data-custom' => 'value',
                'data-test' => 'true'
            ]
        ]);
    }

    /**
     * Fluent API Examples
     */
    public function fluentApiExamples()
    {
        // Basic fluent API
        Alert::success('Operation completed!')
            ->withTitle('Success')
            ->withIcon('fas fa-check')
            ->withClass('custom-success')
            ->withStyle('border-left: 4px solid #28a745;')
            ->dismissible(true)
            ->autoDismiss(true)
            ->autoDismissDelay(5000);

        // Advanced fluent API
        Alert::error('Validation failed!')
            ->withTitle('Error')
            ->withIcon('fas fa-times')
            ->withClass('custom-error')
            ->withStyle('border-left: 4px solid #dc3545;')
            ->withContext('form_validation')
            ->withField('email')
            ->withForm('contact_form')
            ->withPriority(8)
            ->withHtmlContent('<strong>Please check the following fields:</strong><ul><li>Email is required</li><li>Password must be at least 8 characters</li></ul>')
            ->withDataAttribute('data-custom', 'value')
            ->withDataAttribute('data-test', 'true')
            ->withDataAttributes([
                'data-extra' => 'extra_value',
                'data-number' => 123
            ])
            ->dismissible(true)
            ->autoDismiss(false)
            ->withTheme('bootstrap')
            ->withPosition('top-right')
            ->withAnimation('fade')
            ->asInline();
    }

    /**
     * Expiration and Auto-dismiss Examples
     */
    public function expirationExamples()
    {
        // Alert with expiration
        Alert::success('Temporary message!')
            ->expiresIn(3600); // Expires in 1 hour

        // Alert with auto-dismiss
        Alert::info('Auto-dismissing message!')
            ->autoDismiss(true)
            ->autoDismissDelay(3000); // Auto-dismiss in 3 seconds

        // Temporary alert
        Alert::temporary('info', 'This will expire soon!', null, 300); // Expires in 5 minutes

        // Flash alert
        Alert::flash('info', 'This will auto-dismiss!', null, 2000); // Auto-dismiss in 2 seconds
    }

    /**
     * Bulk Operations Examples
     */
    public function bulkOperationsExamples()
    {
        // Create multiple alerts
        $alerts = [
            ['type' => 'success', 'message' => 'First operation completed'],
            ['type' => 'success', 'message' => 'Second operation completed'],
            ['type' => 'warning', 'message' => 'Third operation needs attention']
        ];

        Alert::addMultiple($alerts);

        // Bulk create with options
        $alertsWithOptions = [
            [
                'type' => 'success',
                'message' => 'User created',
                'title' => 'Success',
                'context' => 'user_management'
            ],
            [
                'type' => 'info',
                'message' => 'Email sent',
                'title' => 'Info',
                'context' => 'email_system'
            ]
        ];

        Alert::addMultiple($alertsWithOptions);
    }

    /**
     * Alert Management Examples
     */
    public function alertManagementExamples()
    {
        // Check if alerts exist
        if (Alert::hasAlerts()) {
            $count = Alert::count();
            echo "You have {$count} alerts";
        }

        // Get all alerts
        $alerts = Alert::getAlerts();
        foreach ($alerts as $alert) {
            echo "Alert: {$alert['message']}";
        }

        // Get alerts by type
        $successAlerts = Alert::getAlertsByType('success');
        $errorAlerts = Alert::getAlertsByType('error');

        // Get first and last alerts
        $firstAlert = Alert::first();
        $lastAlert = Alert::last();

        // Clear alerts
        Alert::clear(); // Clear all
        Alert::clearByType('success'); // Clear by type

        // Flush alerts (get and clear)
        $flushedAlerts = Alert::flush();
    }

    /**
     * Toast Alert Examples
     */
    public function toastAlertExamples()
    {
        // Basic toast alerts
        Toast::success('Toast success message!');
        Toast::error('Toast error message!');
        Toast::warning('Toast warning message!');
        Toast::info('Toast info message!');

        // Toast with options
        Toast::success('Toast with options!', 'Success', [
            'position' => 'top-right',
            'auto_dismiss' => true,
            'auto_dismiss_delay' => 3000,
            'animation' => 'slide'
        ]);

        // Toast with fluent API
        Toast::info('Fluent toast!')
            ->withTitle('Info')
            ->withIcon('fas fa-info')
            ->withPosition('bottom-right')
            ->autoDismiss(true)
            ->autoDismissDelay(4000);
    }

    /**
     * Modal Alert Examples
     */
    public function modalAlertExamples()
    {
        // Basic modal alerts
        Modal::success('Modal success message!');
        Modal::error('Modal error message!');
        Modal::warning('Modal warning message!');
        Modal::info('Modal info message!');

        // Modal with options
        Modal::success('Modal with options!', 'Success', [
            'size' => 'lg',
            'backdrop' => 'static',
            'keyboard' => false,
            'show_close_button' => true
        ]);

        // Modal with fluent API
        Modal::error('Fluent modal!')
            ->withTitle('Error')
            ->withIcon('fas fa-times')
            ->withSize('lg')
            ->withBackdrop('static')
            ->withKeyboard(false)
            ->withCloseButton(true);
    }

    /**
     * Inline Alert Examples
     */
    public function inlineAlertExamples()
    {
        // Basic inline alerts
        Inline::success('Inline success message!');
        Inline::error('Inline error message!');
        Inline::warning('Inline warning message!');
        Inline::info('Inline info message!');

        // Inline with options
        Inline::success('Inline with options!', 'Success', [
            'theme' => 'bootstrap',
            'dismissible' => true,
            'auto_dismiss' => true,
            'auto_dismiss_delay' => 5000
        ]);

        // Inline with fluent API
        Inline::warning('Fluent inline!')
            ->withTitle('Warning')
            ->withIcon('fas fa-exclamation')
            ->withTheme('bootstrap')
            ->dismissible(true)
            ->autoDismiss(true)
            ->autoDismissDelay(3000);
    }

    /**
     * Blade Component Examples
     */
    public function bladeComponentExamples()
    {
        // In your Blade templates:

        // Single alert component
        /*
        <x-alert type="success" message="Operation completed!" />
        <x-alert type="error" message="Something went wrong!" title="Error" />
        <x-alert type="warning" message="Please check your input!" dismissible="true" />
        <x-alert type="info" message="New features available!" auto-dismiss="true" auto-dismiss-delay="5000" />
        */

        // Multiple alerts component
        /*
        <x-alerts />
        <x-alerts theme="bootstrap" position="top-right" />
        <x-alerts theme="tailwind" position="bottom-left" animation="slide" />
        */

        // Toast component
        /*
        <x-alert-toast type="success" message="Toast message!" />
        <x-alert-toast type="error" message="Toast error!" position="top-right" />
        */

        // Modal component
        /*
        <x-alert-modal type="success" message="Modal message!" />
        <x-alert-modal type="error" message="Modal error!" size="lg" />
        */

        // Inline component
        /*
        <x-alert-inline type="success" message="Inline message!" />
        <x-alert-inline type="error" message="Inline error!" theme="bootstrap" />
        */
    }

    /**
     * Blade Directive Examples
     */
    public function bladeDirectiveExamples()
    {
        // In your Blade templates:

        // @alert directive
        /*
        @alert('success', 'Operation completed!')
        @alert('error', 'Something went wrong!', 'Error')
        @alert('warning', 'Please check your input!', null, ['dismissible' => true])
        @alert('info', 'New features available!', null, ['auto_dismiss' => true, 'auto_dismiss_delay' => 5000])
        */

        // @alerts directive
        /*
        @alerts
        @alerts(['theme' => 'bootstrap', 'position' => 'top-right'])
        @alerts(['theme' => 'tailwind', 'position' => 'bottom-left', 'animation' => 'slide'])
        */

        // @alertIf directive
        /*
        @alertIf($condition, 'success', 'Condition is true!')
        @alertIf($hasErrors, 'error', 'Please fix the errors!')
        @alertIf($isSuccess, 'success', 'Operation completed!', 'Success', ['dismissible' => true])
        */
    }

    /**
     * JavaScript Integration Examples
     */
    public function javascriptIntegrationExamples()
    {
        // In your JavaScript:

        // Basic JavaScript API
        /*
        // Create alert
        LaravelAlert.success('JavaScript alert!');
        LaravelAlert.error('JavaScript error!');
        LaravelAlert.warning('JavaScript warning!');
        LaravelAlert.info('JavaScript info!');

        // Create alert with options
        LaravelAlert.success('JavaScript alert with options!', {
            title: 'Success',
            dismissible: true,
            autoDismiss: true,
            autoDismissDelay: 5000,
            theme: 'bootstrap',
            position: 'top-right',
            animation: 'fade'
        });

        // Fluent API
        LaravelAlert.success('JavaScript fluent alert!')
            .withTitle('Success')
            .withIcon('fas fa-check')
            .withClass('custom-alert')
            .withStyle('border-left: 4px solid #28a745;')
            .dismissible(true)
            .autoDismiss(true)
            .autoDismissDelay(5000);

        // Get alerts
        const alerts = LaravelAlert.getAlerts();
        const count = LaravelAlert.count();
        const hasAlerts = LaravelAlert.hasAlerts();

        // Clear alerts
        LaravelAlert.clear();
        LaravelAlert.clearByType('success');

        // Dismiss alert
        LaravelAlert.dismiss('alert_id');

        // Dismiss all alerts
        LaravelAlert.dismissAll();
        */

        // AJAX Examples
        /*
        // Create alert via AJAX
        $.post('/api/v1/alerts', {
            type: 'success',
            message: 'AJAX alert!',
            title: 'Success'
        }, function(response) {
            if (response.success) {
                LaravelAlert.success('Alert created via AJAX!');
            }
        });

        // Get alerts via AJAX
        $.get('/api/v1/alerts', function(response) {
            if (response.success) {
                response.data.forEach(function(alert) {
                    LaravelAlert[alert.type](alert.message, alert.title);
                });
            }
        });

        // Dismiss alert via AJAX
        $.post('/api/v1/alerts/alert_id/dismiss', function(response) {
            if (response.success) {
                LaravelAlert.dismiss('alert_id');
            }
        });
        */

        // WebSocket Examples
        /*
        // Connect to WebSocket
        const ws = new WebSocket('ws://localhost:8080');

        ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            if (data.type === 'alert') {
                LaravelAlert[data.alert.type](data.alert.message, data.alert.title);
            }
        };

        // Send alert via WebSocket
        ws.send(JSON.stringify({
            type: 'create_alert',
            alert: {
                type: 'success',
                message: 'WebSocket alert!',
                title: 'Success'
            }
        }));
        */

        // Pusher Examples
        /*
        // Initialize Pusher
        const pusher = new Pusher('your-pusher-key', {
            cluster: 'mt1'
        });

        // Subscribe to alerts channel
        const channel = pusher.subscribe('alerts');

        // Listen for alert events
        channel.bind('alert.created', function(data) {
            LaravelAlert[data.alert.type](data.alert.message, data.alert.title);
        });

        channel.bind('alert.dismissed', function(data) {
            LaravelAlert.dismiss(data.alert_id);
        });

        // Send alert via Pusher
        channel.trigger('client-alert-created', {
            type: 'success',
            message: 'Pusher alert!',
            title: 'Success'
        });
        */
    }

    /**
     * API Integration Examples
     */
    public function apiIntegrationExamples()
    {
        // REST API Examples
        /*
        // Create alert
        POST /api/v1/alerts
        {
            "type": "success",
            "message": "API alert!",
            "title": "Success",
            "dismissible": true,
            "auto_dismiss": false
        }

        // Get alerts
        GET /api/v1/alerts
        GET /api/v1/alerts?type=success
        GET /api/v1/alerts?page=1&per_page=15
        GET /api/v1/alerts?sort=created_at&order=desc

        // Get specific alert
        GET /api/v1/alerts/alert_id

        // Update alert
        PUT /api/v1/alerts/alert_id
        {
            "message": "Updated message",
            "title": "Updated title"
        }

        // Delete alert
        DELETE /api/v1/alerts/alert_id

        // Dismiss alert
        POST /api/v1/alerts/alert_id/dismiss

        // Dismiss all alerts
        POST /api/v1/alerts/dismiss-all

        // Clear all alerts
        DELETE /api/v1/alerts/clear

        // Get alerts by type
        GET /api/v1/alerts/type/success

        // Get alert statistics
        GET /api/v1/alerts/stats/overview

        // Get alert history
        GET /api/v1/alerts/history/audit

        // Bulk operations
        POST /api/v1/alerts/bulk/create
        {
            "alerts": [
                {
                    "type": "success",
                    "message": "First alert"
                },
                {
                    "type": "error",
                    "message": "Second alert"
                }
            ]
        }

        PATCH /api/v1/alerts/bulk/update
        {
            "alerts": [
                {
                    "id": "alert_123",
                    "message": "Updated first alert"
                },
                {
                    "id": "alert_124",
                    "message": "Updated second alert"
                }
            ]
        }
        */
    }

    /**
     * Email Integration Examples
     */
    public function emailIntegrationExamples()
    {
        // Email alert examples
        /*
        // Send single alert email
        Alert::success('Email alert!')
            ->sendEmail(['admin@example.com']);

        // Send alert to specific user
        Alert::success('User alert!')
            ->sendEmailToUser($userId);

        // Send alert to role
        Alert::success('Role alert!')
            ->sendEmailToRole('admin');

        // Send alert to permission
        Alert::success('Permission alert!')
            ->sendEmailToPermission('manage_alerts');

        // Send alert to group
        Alert::success('Group alert!')
            ->sendEmailToGroup('developers');

        // Send multiple alerts email
        Alert::sendMultipleAlertsEmail([
            ['type' => 'success', 'message' => 'First alert'],
            ['type' => 'error', 'message' => 'Second alert']
        ], ['admin@example.com']);

        // Send alert summary email
        Alert::sendAlertSummaryEmail([
            'total_alerts' => 100,
            'success_count' => 40,
            'error_count' => 30,
            'warning_count' => 20,
            'info_count' => 10
        ], ['admin@example.com']);
        */
    }

    /**
     * Configuration Examples
     */
    public function configurationExamples()
    {
        // Environment variables
        /*
        # Alert Configuration
        LARAVEL_ALERT_ENABLED=true
        LARAVEL_ALERT_THEME=bootstrap
        LARAVEL_ALERT_POSITION=top-right
        LARAVEL_ALERT_DISMISSIBLE=true
        LARAVEL_ALERT_AUTO_DISMISS=true
        LARAVEL_ALERT_AUTO_DISMISS_DELAY=5000
        LARAVEL_ALERT_ANIMATION=fade
        LARAVEL_ALERT_SESSION_KEY=laravel_alerts

        # JavaScript Configuration
        LARAVEL_ALERT_JAVASCRIPT_ENABLED=true
        LARAVEL_ALERT_JAVASCRIPT_AUTO_DISMISS=true
        LARAVEL_ALERT_JAVASCRIPT_AUTO_DISMISS_DELAY=5000
        LARAVEL_ALERT_JAVASCRIPT_ANIMATION=fade
        LARAVEL_ALERT_JAVASCRIPT_POSITION=top-right

        # Storage Configuration
        LARAVEL_ALERT_STORAGE_DRIVER=database
        LARAVEL_ALERT_STORAGE_FALLBACK=session
        LARAVEL_ALERT_STORAGE_PERSISTENCE=true
        LARAVEL_ALERT_CLEANUP_INTERVAL=3600
        LARAVEL_ALERT_MAX_ALERTS_PER_USER=1000
        LARAVEL_ALERT_MAX_ALERTS_PER_SESSION=100

        # Cache Configuration
        LARAVEL_ALERT_CACHE_ENABLED=true
        LARAVEL_ALERT_CACHE_DRIVER=file
        LARAVEL_ALERT_CACHE_PREFIX=laravel_alert
        LARAVEL_ALERT_CACHE_TTL=3600

        # Redis Configuration
        LARAVEL_ALERT_REDIS_ENABLED=false
        LARAVEL_ALERT_REDIS_CONNECTION=default
        LARAVEL_ALERT_REDIS_PREFIX=laravel_alert
        LARAVEL_ALERT_REDIS_TTL=3600

        # Performance Configuration
        LARAVEL_ALERT_BATCH_PROCESSING=true
        LARAVEL_ALERT_LAZY_LOADING=true
        LARAVEL_ALERT_QUERY_OPTIMIZATION=true
        LARAVEL_ALERT_CACHE_WARMING=true
        LARAVEL_ALERT_INDEX_OPTIMIZATION=true
        LARAVEL_ALERT_MEMORY_OPTIMIZATION=true
        LARAVEL_ALERT_CONNECTION_POOLING=true
        LARAVEL_ALERT_COMPRESSION=true

        # Pusher Configuration
        LARAVEL_ALERT_PUSHER_ENABLED=false
        PUSHER_APP_KEY=your-pusher-key
        PUSHER_APP_SECRET=your-pusher-secret
        PUSHER_APP_ID=your-pusher-app-id
        PUSHER_APP_CLUSTER=mt1

        # WebSocket Configuration
        LARAVEL_ALERT_WEBSOCKET_ENABLED=false
        LARAVEL_ALERT_WEBSOCKET_DRIVER=redis
        LARAVEL_ALERT_WEBSOCKET_HOST=localhost
        LARAVEL_ALERT_WEBSOCKET_PORT=8080

        # Email Configuration
        LARAVEL_ALERT_EMAIL_ENABLED=false
        LARAVEL_ALERT_EMAIL_DEFAULT_RECIPIENTS=admin@example.com
        LARAVEL_ALERT_EMAIL_SUBJECT_PREFIX=[Alert]
        LARAVEL_ALERT_EMAIL_SCHEDULING_ENABLED=false
        LARAVEL_ALERT_EMAIL_SCHEDULING_FREQUENCY=daily
        LARAVEL_ALERT_EMAIL_SCHEDULING_TIME=09:00
        LARAVEL_ALERT_EMAIL_SCHEDULING_TIMEZONE=UTC
        */
    }

    /**
     * Testing Examples
     */
    public function testingExamples()
    {
        // Unit testing examples
        /*
        // Test alert creation
        public function test_can_create_success_alert()
        {
            Alert::success('Test message');
            
            $alerts = Alert::getAlerts();
            $this->assertCount(1, $alerts);
            $this->assertEquals('success', $alerts[0]['type']);
            $this->assertEquals('Test message', $alerts[0]['message']);
        }

        // Test alert options
        public function test_can_create_alert_with_options()
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

        // Test fluent API
        public function test_can_use_fluent_api()
        {
            Alert::success('Test message')
                ->withTitle('Test Title')
                ->withIcon('fas fa-check')
                ->dismissible(true)
                ->autoDismiss(true)
                ->autoDismissDelay(5000);
            
            $alerts = Alert::getAlerts();
            $this->assertCount(1, $alerts);
            $this->assertEquals('Test Title', $alerts[0]['title']);
            $this->assertEquals('fas fa-check', $alerts[0]['icon']);
            $this->assertTrue($alerts[0]['dismissible']);
            $this->assertTrue($alerts[0]['auto_dismiss']);
            $this->assertEquals(5000, $alerts[0]['auto_dismiss_delay']);
        }

        // Test alert management
        public function test_can_manage_alerts()
        {
            Alert::success('First alert');
            Alert::error('Second alert');
            
            $this->assertTrue(Alert::hasAlerts());
            $this->assertEquals(2, Alert::count());
            
            $alerts = Alert::getAlerts();
            $this->assertCount(2, $alerts);
            
            $successAlerts = Alert::getAlertsByType('success');
            $this->assertCount(1, $successAlerts);
            
            Alert::clearByType('success');
            $this->assertEquals(1, Alert::count());
            
            Alert::clear();
            $this->assertFalse(Alert::hasAlerts());
        }

        // Test alert rendering
        public function test_can_render_alerts()
        {
            Alert::success('Test message');
            
            $html = Alert::renderAll();
            $this->assertStringContainsString('Test message', $html);
            $this->assertStringContainsString('alert-success', $html);
        }
        */
    }

    /**
     * Performance Examples
     */
    public function performanceExamples()
    {
        // Performance optimization examples
        /*
        // Enable performance optimizations
        config(['laravel-alert.performance.batch_processing' => true]);
        config(['laravel-alert.performance.lazy_loading' => true]);
        config(['laravel-alert.performance.query_optimization' => true]);
        config(['laravel-alert.performance.memory_optimization' => true]);
        config(['laravel-alert.performance.connection_pooling' => true]);
        config(['laravel-alert.performance.compression' => true]);

        // Batch processing
        $alerts = [];
        for ($i = 0; $i < 1000; $i++) {
            $alerts[] = [
                'type' => 'success',
                'message' => "Alert {$i}"
            ];
        }
        Alert::addMultiple($alerts);

        // Memory optimization
        Alert::success('Memory optimized alert!')
            ->withMemoryOptimization(true);

        // Query optimization
        Alert::success('Query optimized alert!')
            ->withQueryOptimization(true);

        // Cache optimization
        Alert::success('Cache optimized alert!')
            ->withCacheOptimization(true);
        */
    }

    /**
     * Security Examples
     */
    public function securityExamples()
    {
        // Security examples
        /*
        // XSS Protection
        Alert::success('<script>alert("XSS")</script>'); // Automatically escaped

        // CSRF Protection
        Alert::success('CSRF protected alert!')
            ->withCsrfProtection(true);

        // Rate Limiting
        Alert::success('Rate limited alert!')
            ->withRateLimiting(true);

        // Input Validation
        Alert::success('Validated alert!')
            ->withInputValidation(true);

        // Authentication
        Alert::success('Authenticated alert!')
            ->withAuthentication(true);

        // Authorization
        Alert::success('Authorized alert!')
            ->withAuthorization(true);
        */
    }

    /**
     * Accessibility Examples
     */
    public function accessibilityExamples()
    {
        // Accessibility examples
        /*
        // ARIA Labels
        Alert::success('Accessible alert!')
            ->withAriaLabel('Success message');

        // Keyboard Navigation
        Alert::success('Keyboard accessible alert!')
            ->withKeyboardNavigation(true);

        // Screen Reader Support
        Alert::success('Screen reader friendly alert!')
            ->withScreenReaderSupport(true);

        // High Contrast Mode
        Alert::success('High contrast alert!')
            ->withHighContrastMode(true);

        // Focus Management
        Alert::success('Focus managed alert!')
            ->withFocusManagement(true);
        */
    }

    /**
     * Internationalization Examples
     */
    public function internationalizationExamples()
    {
        // Internationalization examples
        /*
        // Multi-language support
        Alert::success('Multi-language alert!')
            ->withLanguage('en')
            ->withTranslation('alert.success.message');

        // Locale-specific formatting
        Alert::success('Localized alert!')
            ->withLocale('en_US')
            ->withDateFormat('Y-m-d H:i:s')
            ->withNumberFormat('#,##0.00');

        // RTL Support
        Alert::success('RTL alert!')
            ->withRtlSupport(true);

        // Unicode Support
        Alert::success('Unicode alert: 测试 🚀 émojis')
            ->withUnicodeSupport(true);
        */
    }

    /**
     * Customization Examples
     */
    public function customizationExamples()
    {
        // Customization examples
        /*
        // Custom themes
        Alert::success('Custom themed alert!')
            ->withTheme('custom-theme');

        // Custom animations
        Alert::success('Custom animated alert!')
            ->withAnimation('custom-animation');

        // Custom positions
        Alert::success('Custom positioned alert!')
            ->withPosition('custom-position');

        // Custom icons
        Alert::success('Custom icon alert!')
            ->withIcon('custom-icon');

        // Custom styles
        Alert::success('Custom styled alert!')
            ->withStyle('custom-style');

        // Custom classes
        Alert::success('Custom class alert!')
            ->withClass('custom-class');

        // Custom templates
        Alert::success('Custom template alert!')
            ->withTemplate('custom-template');
        */
    }
}
