<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Wahyudedik\LaravelAlert\AlertServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Start session for testing
        Session::start();

        // Register the service provider
        $this->app->register(AlertServiceProvider::class);

        // Run migrations
        Artisan::call('migrate');

        // Clear any existing alerts
        Session::forget('laravel_alerts');
    }

    protected function tearDown(): void
    {
        // Clear session
        Session::flush();

        parent::tearDown();
    }

    /**
     * Create a test alert.
     */
    protected function createTestAlert(string $type = 'success', string $message = 'Test message', array $options = []): array
    {
        return [
            'id' => uniqid(),
            'type' => $type,
            'message' => $message,
            'title' => $options['title'] ?? null,
            'dismissible' => $options['dismissible'] ?? true,
            'auto_dismiss' => $options['auto_dismiss'] ?? false,
            'auto_dismiss_delay' => $options['auto_dismiss_delay'] ?? 5000,
            'theme' => $options['theme'] ?? 'bootstrap',
            'position' => $options['position'] ?? 'top-right',
            'animation' => $options['animation'] ?? 'fade',
            'icon' => $options['icon'] ?? null,
            'class' => $options['class'] ?? null,
            'style' => $options['style'] ?? null,
            'data_attributes' => $options['data_attributes'] ?? [],
            'context' => $options['context'] ?? null,
            'field' => $options['field'] ?? null,
            'form' => $options['form'] ?? null,
            'priority' => $options['priority'] ?? 1,
            'html_content' => $options['html_content'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];
    }

    /**
     * Create multiple test alerts.
     */
    protected function createMultipleTestAlerts(int $count = 5, array $types = ['success', 'error', 'warning', 'info']): array
    {
        $alerts = [];
        for ($i = 0; $i < $count; $i++) {
            $type = $types[$i % count($types)];
            $alerts[] = $this->createTestAlert($type, "Test Alert {$i}");
        }
        return $alerts;
    }

    /**
     * Assert that an alert exists in the session.
     */
    protected function assertAlertExists(string $message, string $type = null): void
    {
        $alerts = Session::get('laravel_alerts', []);
        $found = false;

        foreach ($alerts as $alert) {
            if ($alert['message'] === $message && ($type === null || $alert['type'] === $type)) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found, "Alert with message '{$message}' and type '{$type}' not found");
    }

    /**
     * Assert that an alert does not exist in the session.
     */
    protected function assertAlertNotExists(string $message, string $type = null): void
    {
        $alerts = Session::get('laravel_alerts', []);
        $found = false;

        foreach ($alerts as $alert) {
            if ($alert['message'] === $message && ($type === null || $alert['type'] === $type)) {
                $found = true;
                break;
            }
        }

        $this->assertFalse($found, "Alert with message '{$message}' and type '{$type}' should not exist");
    }

    /**
     * Assert that the alert count matches the expected count.
     */
    protected function assertAlertCount(int $expectedCount): void
    {
        $alerts = Session::get('laravel_alerts', []);
        $this->assertCount($expectedCount, $alerts, "Expected {$expectedCount} alerts, but found " . count($alerts));
    }

    /**
     * Assert that alerts of a specific type exist.
     */
    protected function assertAlertsOfType(string $type, int $expectedCount): void
    {
        $alerts = Session::get('laravel_alerts', []);
        $typeAlerts = array_filter($alerts, function ($alert) use ($type) {
            return $alert['type'] === $type;
        });

        $this->assertCount($expectedCount, $typeAlerts, "Expected {$expectedCount} alerts of type '{$type}', but found " . count($typeAlerts));
    }

    /**
     * Assert that an alert has specific properties.
     */
    protected function assertAlertHasProperties(array $expectedProperties): void
    {
        $alerts = Session::get('laravel_alerts', []);
        $found = false;

        foreach ($alerts as $alert) {
            $matches = true;
            foreach ($expectedProperties as $key => $value) {
                if (!isset($alert[$key]) || $alert[$key] !== $value) {
                    $matches = false;
                    break;
                }
            }
            if ($matches) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found, 'Alert with expected properties not found');
    }

    /**
     * Assert that an alert HTML contains specific content.
     */
    protected function assertAlertHtmlContains(string $html, string $content): void
    {
        $this->assertStringContainsString($content, $html, "Alert HTML should contain '{$content}'");
    }

    /**
     * Assert that an alert HTML does not contain specific content.
     */
    protected function assertAlertHtmlNotContains(string $html, string $content): void
    {
        $this->assertStringNotContainsString($content, $html, "Alert HTML should not contain '{$content}'");
    }

    /**
     * Assert that an alert has specific CSS classes.
     */
    protected function assertAlertHasClass(string $html, string $className): void
    {
        $this->assertStringContainsString($className, $html, "Alert HTML should contain class '{$className}'");
    }

    /**
     * Assert that an alert has specific data attributes.
     */
    protected function assertAlertHasDataAttribute(string $html, string $attribute, string $value): void
    {
        $this->assertStringContainsString("data-{$attribute}=\"{$value}\"", $html, "Alert HTML should contain data-{$attribute}=\"{$value}\"");
    }

    /**
     * Assert that an alert has specific inline styles.
     */
    protected function assertAlertHasStyle(string $html, string $style): void
    {
        $this->assertStringContainsString($style, $html, "Alert HTML should contain style '{$style}'");
    }

    /**
     * Assert that an alert has specific icon.
     */
    protected function assertAlertHasIcon(string $html, string $icon): void
    {
        $this->assertStringContainsString($icon, $html, "Alert HTML should contain icon '{$icon}'");
    }

    /**
     * Assert that an alert has specific title.
     */
    protected function assertAlertHasTitle(string $html, string $title): void
    {
        $this->assertStringContainsString($title, $html, "Alert HTML should contain title '{$title}'");
    }

    /**
     * Assert that an alert has specific message.
     */
    protected function assertAlertHasMessage(string $html, string $message): void
    {
        $this->assertStringContainsString($message, $html, "Alert HTML should contain message '{$message}'");
    }

    /**
     * Assert that an alert has specific context.
     */
    protected function assertAlertHasContext(string $html, string $context): void
    {
        $this->assertStringContainsString($context, $html, "Alert HTML should contain context '{$context}'");
    }

    /**
     * Assert that an alert has specific field.
     */
    protected function assertAlertHasField(string $html, string $field): void
    {
        $this->assertStringContainsString($field, $html, "Alert HTML should contain field '{$field}'");
    }

    /**
     * Assert that an alert has specific form.
     */
    protected function assertAlertHasForm(string $html, string $form): void
    {
        $this->assertStringContainsString($form, $html, "Alert HTML should contain form '{$form}'");
    }

    /**
     * Assert that an alert has specific priority.
     */
    protected function assertAlertHasPriority(string $html, int $priority): void
    {
        $this->assertStringContainsString("priority-{$priority}", $html, "Alert HTML should contain priority-{$priority}");
    }

    /**
     * Assert that an alert has specific animation.
     */
    protected function assertAlertHasAnimation(string $html, string $animation): void
    {
        $this->assertStringContainsString($animation, $html, "Alert HTML should contain animation '{$animation}'");
    }

    /**
     * Assert that an alert has specific position.
     */
    protected function assertAlertHasPosition(string $html, string $position): void
    {
        $this->assertStringContainsString($position, $html, "Alert HTML should contain position '{$position}'");
    }

    /**
     * Assert that an alert has specific theme.
     */
    protected function assertAlertHasTheme(string $html, string $theme): void
    {
        $this->assertStringContainsString($theme, $html, "Alert HTML should contain theme '{$theme}'");
    }

    /**
     * Assert that an alert is dismissible.
     */
    protected function assertAlertIsDismissible(string $html): void
    {
        $this->assertStringContainsString('alert-dismissible', $html, 'Alert should be dismissible');
        $this->assertStringContainsString('btn-close', $html, 'Alert should have close button');
    }

    /**
     * Assert that an alert is not dismissible.
     */
    protected function assertAlertIsNotDismissible(string $html): void
    {
        $this->assertStringNotContainsString('alert-dismissible', $html, 'Alert should not be dismissible');
        $this->assertStringNotContainsString('btn-close', $html, 'Alert should not have close button');
    }

    /**
     * Assert that an alert has auto-dismiss functionality.
     */
    protected function assertAlertHasAutoDismiss(string $html, int $delay = null): void
    {
        $this->assertStringContainsString('data-auto-dismiss="true"', $html, 'Alert should have auto-dismiss enabled');
        if ($delay !== null) {
            $this->assertStringContainsString("data-auto-dismiss-delay=\"{$delay}\"", $html, "Alert should have auto-dismiss delay of {$delay}ms");
        }
    }

    /**
     * Assert that an alert does not have auto-dismiss functionality.
     */
    protected function assertAlertHasNoAutoDismiss(string $html): void
    {
        $this->assertStringNotContainsString('data-auto-dismiss="true"', $html, 'Alert should not have auto-dismiss enabled');
    }

    /**
     * Assert that an alert has expiration.
     */
    protected function assertAlertHasExpiration(string $html, string $expiresAt): void
    {
        $this->assertStringContainsString("data-expires-at=\"{$expiresAt}\"", $html, "Alert should have expiration time of {$expiresAt}");
    }

    /**
     * Assert that an alert does not have expiration.
     */
    protected function assertAlertHasNoExpiration(string $html): void
    {
        $this->assertStringNotContainsString('data-expires-at', $html, 'Alert should not have expiration time');
    }

    /**
     * Assert that an alert has HTML content.
     */
    protected function assertAlertHasHtmlContent(string $html, string $content): void
    {
        $this->assertStringContainsString($content, $html, "Alert HTML should contain HTML content '{$content}'");
    }

    /**
     * Assert that an alert has specific alert type.
     */
    protected function assertAlertHasType(string $html, string $type): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
    }

    /**
     * Assert that an alert has specific alert type with theme.
     */
    protected function assertAlertHasTypeWithTheme(string $html, string $type, string $theme): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString($theme, $html, "Alert HTML should contain theme '{$theme}'");
    }

    /**
     * Assert that an alert has specific alert type with position.
     */
    protected function assertAlertHasTypeWithPosition(string $html, string $type, string $position): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString($position, $html, "Alert HTML should contain position '{$position}'");
    }

    /**
     * Assert that an alert has specific alert type with animation.
     */
    protected function assertAlertHasTypeWithAnimation(string $html, string $type, string $animation): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString($animation, $html, "Alert HTML should contain animation '{$animation}'");
    }

    /**
     * Assert that an alert has specific alert type with icon.
     */
    protected function assertAlertHasTypeWithIcon(string $html, string $type, string $icon): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString($icon, $html, "Alert HTML should contain icon '{$icon}'");
    }

    /**
     * Assert that an alert has specific alert type with class.
     */
    protected function assertAlertHasTypeWithClass(string $html, string $type, string $class): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString($class, $html, "Alert HTML should contain class '{$class}'");
    }

    /**
     * Assert that an alert has specific alert type with style.
     */
    protected function assertAlertHasTypeWithStyle(string $html, string $type, string $style): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString($style, $html, "Alert HTML should contain style '{$style}'");
    }

    /**
     * Assert that an alert has specific alert type with data attributes.
     */
    protected function assertAlertHasTypeWithDataAttributes(string $html, string $type, array $dataAttributes): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        foreach ($dataAttributes as $attribute => $value) {
            $this->assertStringContainsString("data-{$attribute}=\"{$value}\"", $html, "Alert HTML should contain data-{$attribute}=\"{$value}\"");
        }
    }

    /**
     * Assert that an alert has specific alert type with context.
     */
    protected function assertAlertHasTypeWithContext(string $html, string $type, string $context): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString($context, $html, "Alert HTML should contain context '{$context}'");
    }

    /**
     * Assert that an alert has specific alert type with field.
     */
    protected function assertAlertHasTypeWithField(string $html, string $type, string $field): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString($field, $html, "Alert HTML should contain field '{$field}'");
    }

    /**
     * Assert that an alert has specific alert type with form.
     */
    protected function assertAlertHasTypeWithForm(string $html, string $type, string $form): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString($form, $html, "Alert HTML should contain form '{$form}'");
    }

    /**
     * Assert that an alert has specific alert type with priority.
     */
    protected function assertAlertHasTypeWithPriority(string $html, string $type, int $priority): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString("priority-{$priority}", $html, "Alert HTML should contain priority-{$priority}");
    }

    /**
     * Assert that an alert has specific alert type with HTML content.
     */
    protected function assertAlertHasTypeWithHtmlContent(string $html, string $type, string $content): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString($content, $html, "Alert HTML should contain HTML content '{$content}'");
    }

    /**
     * Assert that an alert has specific alert type with expiration.
     */
    protected function assertAlertHasTypeWithExpiration(string $html, string $type, string $expiresAt): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString("data-expires-at=\"{$expiresAt}\"", $html, "Alert HTML should contain expiration time of {$expiresAt}");
    }

    /**
     * Assert that an alert has specific alert type with auto-dismiss.
     */
    protected function assertAlertHasTypeWithAutoDismiss(string $html, string $type, int $delay = null): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        $this->assertStringContainsString('data-auto-dismiss="true"', $html, 'Alert should have auto-dismiss enabled');
        if ($delay !== null) {
            $this->assertStringContainsString("data-auto-dismiss-delay=\"{$delay}\"", $html, "Alert should have auto-dismiss delay of {$delay}ms");
        }
    }

    /**
     * Assert that an alert has specific alert type with dismissible.
     */
    protected function assertAlertHasTypeWithDismissible(string $html, string $type, bool $dismissible = true): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");
        if ($dismissible) {
            $this->assertStringContainsString('alert-dismissible', $html, 'Alert should be dismissible');
            $this->assertStringContainsString('btn-close', $html, 'Alert should have close button');
        } else {
            $this->assertStringNotContainsString('alert-dismissible', $html, 'Alert should not be dismissible');
            $this->assertStringNotContainsString('btn-close', $html, 'Alert should not have close button');
        }
    }

    /**
     * Assert that an alert has specific alert type with all properties.
     */
    protected function assertAlertHasTypeWithAllProperties(string $html, string $type, array $properties): void
    {
        $this->assertStringContainsString("alert-{$type}", $html, "Alert HTML should contain alert-{$type} class");

        foreach ($properties as $key => $value) {
            switch ($key) {
                case 'title':
                    $this->assertStringContainsString($value, $html, "Alert HTML should contain title '{$value}'");
                    break;
                case 'message':
                    $this->assertStringContainsString($value, $html, "Alert HTML should contain message '{$value}'");
                    break;
                case 'icon':
                    $this->assertStringContainsString($value, $html, "Alert HTML should contain icon '{$value}'");
                    break;
                case 'class':
                    $this->assertStringContainsString($value, $html, "Alert HTML should contain class '{$value}'");
                    break;
                case 'style':
                    $this->assertStringContainsString($value, $html, "Alert HTML should contain style '{$value}'");
                    break;
                case 'context':
                    $this->assertStringContainsString($value, $html, "Alert HTML should contain context '{$value}'");
                    break;
                case 'field':
                    $this->assertStringContainsString($value, $html, "Alert HTML should contain field '{$value}'");
                    break;
                case 'form':
                    $this->assertStringContainsString($value, $html, "Alert HTML should contain form '{$value}'");
                    break;
                case 'priority':
                    $this->assertStringContainsString("priority-{$value}", $html, "Alert HTML should contain priority-{$value}");
                    break;
                case 'html_content':
                    $this->assertStringContainsString($value, $html, "Alert HTML should contain HTML content '{$value}'");
                    break;
                case 'expires_at':
                    $this->assertStringContainsString("data-expires-at=\"{$value}\"", $html, "Alert HTML should contain expiration time of {$value}");
                    break;
                case 'auto_dismiss':
                    if ($value) {
                        $this->assertStringContainsString('data-auto-dismiss="true"', $html, 'Alert should have auto-dismiss enabled');
                    } else {
                        $this->assertStringNotContainsString('data-auto-dismiss="true"', $html, 'Alert should not have auto-dismiss enabled');
                    }
                    break;
                case 'auto_dismiss_delay':
                    $this->assertStringContainsString("data-auto-dismiss-delay=\"{$value}\"", $html, "Alert should have auto-dismiss delay of {$value}ms");
                    break;
                case 'dismissible':
                    if ($value) {
                        $this->assertStringContainsString('alert-dismissible', $html, 'Alert should be dismissible');
                        $this->assertStringContainsString('btn-close', $html, 'Alert should have close button');
                    } else {
                        $this->assertStringNotContainsString('alert-dismissible', $html, 'Alert should not be dismissible');
                        $this->assertStringNotContainsString('btn-close', $html, 'Alert should not have close button');
                    }
                    break;
                case 'data_attributes':
                    foreach ($value as $attribute => $attrValue) {
                        $this->assertStringContainsString("data-{$attribute}=\"{$attrValue}\"", $html, "Alert HTML should contain data-{$attribute}=\"{$attrValue}\"");
                    }
                    break;
            }
        }
    }
}
