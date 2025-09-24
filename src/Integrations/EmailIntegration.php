<?php

namespace Wahyudedik\LaravelAlert\Integrations;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EmailIntegration
{
    protected array $config;
    protected bool $enabled;
    protected string $driver;

    public function __construct()
    {
        $this->config = config('laravel-alert.email', []);
        $this->enabled = $this->config['enabled'] ?? false;
        $this->driver = $this->config['driver'] ?? 'smtp';
    }

    /**
     * Send alert via email.
     */
    public function sendAlert(array $alert, array $recipients = [], array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $recipients = $recipients ?: $this->getDefaultRecipients();
            $template = $options['template'] ?? $this->getDefaultTemplate();
            $subject = $options['subject'] ?? $this->getDefaultSubject($alert);

            $mailData = [
                'alert' => $alert,
                'recipients' => $recipients,
                'options' => $options,
                'timestamp' => now()->toISOString()
            ];

            $mailable = new AlertEmailMailable($mailData, $template, $subject);

            foreach ($recipients as $recipient) {
                Mail::to($recipient)->send($mailable);
            }

            Log::info('Alert email sent successfully', [
                'alert_id' => $alert['id'] ?? null,
                'recipients_count' => count($recipients),
                'template' => $template
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send alert email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send multiple alerts via email.
     */
    public function sendMultipleAlerts(array $alerts, array $recipients = [], array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $recipients = $recipients ?: $this->getDefaultRecipients();
            $template = $options['template'] ?? $this->getDefaultTemplate();
            $subject = $options['subject'] ?? $this->getDefaultSubject(['type' => 'multiple']);

            $mailData = [
                'alerts' => $alerts,
                'recipients' => $recipients,
                'options' => $options,
                'timestamp' => now()->toISOString()
            ];

            $mailable = new MultipleAlertsEmailMailable($mailData, $template, $subject);

            foreach ($recipients as $recipient) {
                Mail::to($recipient)->send($mailable);
            }

            Log::info('Multiple alerts email sent successfully', [
                'alerts_count' => count($alerts),
                'recipients_count' => count($recipients),
                'template' => $template
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send multiple alerts email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send alert summary email.
     */
    public function sendAlertSummary(array $summary, array $recipients = [], array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $recipients = $recipients ?: $this->getDefaultRecipients();
            $template = $options['template'] ?? 'laravel-alert::emails.summary';
            $subject = $options['subject'] ?? 'Alert Summary - ' . now()->format('Y-m-d');

            $mailData = [
                'summary' => $summary,
                'recipients' => $recipients,
                'options' => $options,
                'timestamp' => now()->toISOString()
            ];

            $mailable = new AlertSummaryEmailMailable($mailData, $template, $subject);

            foreach ($recipients as $recipient) {
                Mail::to($recipient)->send($mailable);
            }

            Log::info('Alert summary email sent successfully', [
                'summary' => $summary,
                'recipients_count' => count($recipients),
                'template' => $template
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send alert summary email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send alert to specific user.
     */
    public function sendAlertToUser(int $userId, array $alert, array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $user = $this->getUser($userId);
            if (!$user) {
                Log::warning('User not found for alert email', ['user_id' => $userId]);
                return false;
            }

            $recipients = [$user->email];
            return $this->sendAlert($alert, $recipients, $options);
        } catch (\Exception $e) {
            Log::error('Failed to send alert email to user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send alert to specific role.
     */
    public function sendAlertToRole(string $role, array $alert, array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $users = $this->getUsersByRole($role);
            if (empty($users)) {
                Log::warning('No users found for role', ['role' => $role]);
                return false;
            }

            $recipients = array_column($users, 'email');
            return $this->sendAlert($alert, $recipients, $options);
        } catch (\Exception $e) {
            Log::error('Failed to send alert email to role: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send alert to specific permission.
     */
    public function sendAlertToPermission(string $permission, array $alert, array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $users = $this->getUsersByPermission($permission);
            if (empty($users)) {
                Log::warning('No users found for permission', ['permission' => $permission]);
                return false;
            }

            $recipients = array_column($users, 'email');
            return $this->sendAlert($alert, $recipients, $options);
        } catch (\Exception $e) {
            Log::error('Failed to send alert email to permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send alert to specific group.
     */
    public function sendAlertToGroup(string $group, array $alert, array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $users = $this->getUsersByGroup($group);
            if (empty($users)) {
                Log::warning('No users found for group', ['group' => $group]);
                return false;
            }

            $recipients = array_column($users, 'email');
            return $this->sendAlert($alert, $recipients, $options);
        } catch (\Exception $e) {
            Log::error('Failed to send alert email to group: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get default recipients.
     */
    protected function getDefaultRecipients(): array
    {
        $recipients = $this->config['default_recipients'] ?? [];

        if (empty($recipients)) {
            $adminEmail = config('mail.from.address');
            if ($adminEmail) {
                $recipients = [$adminEmail];
            }
        }

        return $recipients;
    }

    /**
     * Get default template.
     */
    protected function getDefaultTemplate(): string
    {
        return $this->config['default_template'] ?? 'laravel-alert::emails.alert';
    }

    /**
     * Get default subject.
     */
    protected function getDefaultSubject(array $alert): string
    {
        $type = $alert['type'] ?? 'alert';
        $title = $alert['title'] ?? ucfirst($type) . ' Alert';

        return $this->config['subject_prefix'] . $title;
    }

    /**
     * Get user by ID.
     */
    protected function getUser(int $userId): ?object
    {
        $userModel = $this->config['user_model'] ?? 'App\\Models\\User';

        if (class_exists($userModel)) {
            return $userModel::find($userId);
        }

        return null;
    }

    /**
     * Get users by role.
     */
    protected function getUsersByRole(string $role): array
    {
        $userModel = $this->config['user_model'] ?? 'App\\Models\\User';

        if (class_exists($userModel)) {
            $users = $userModel::whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            })->get();

            return $users->toArray();
        }

        return [];
    }

    /**
     * Get users by permission.
     */
    protected function getUsersByPermission(string $permission): array
    {
        $userModel = $this->config['user_model'] ?? 'App\\Models\\User';

        if (class_exists($userModel)) {
            $users = $userModel::whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })->get();

            return $users->toArray();
        }

        return [];
    }

    /**
     * Get users by group.
     */
    protected function getUsersByGroup(string $group): array
    {
        $userModel = $this->config['user_model'] ?? 'App\\Models\\User';

        if (class_exists($userModel)) {
            $users = $userModel::whereHas('groups', function ($query) use ($group) {
                $query->where('name', $group);
            })->get();

            return $users->toArray();
        }

        return [];
    }

    /**
     * Check if email integration is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get email configuration.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get email driver.
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * Test email connection.
     */
    public function testConnection(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $testAlert = [
                'id' => 'test_' . time(),
                'type' => 'info',
                'message' => 'This is a test email',
                'title' => 'Test Alert',
                'timestamp' => now()->toISOString()
            ];

            $testRecipients = $this->getDefaultRecipients();
            if (empty($testRecipients)) {
                return false;
            }

            return $this->sendAlert($testAlert, $testRecipients, [
                'template' => 'laravel-alert::emails.test',
                'subject' => 'Test Email - Laravel Alert'
            ]);
        } catch (\Exception $e) {
            Log::error('Email connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get connection status.
     */
    public function getConnectionStatus(): array
    {
        if (!$this->enabled) {
            return [
                'enabled' => false,
                'status' => 'disabled',
                'message' => 'Email integration is disabled'
            ];
        }

        try {
            $testResult = $this->testConnection();
            return [
                'enabled' => true,
                'status' => $testResult ? 'connected' : 'disconnected',
                'message' => $testResult ? 'Email connection successful' : 'Email connection failed',
                'driver' => $this->driver,
                'config' => $this->config
            ];
        } catch (\Exception $e) {
            return [
                'enabled' => true,
                'status' => 'error',
                'message' => 'Email connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Enable email integration.
     */
    public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * Disable email integration.
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Update email configuration.
     */
    public function updateConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }
}

/**
 * Alert Email Mailable
 */
class AlertEmailMailable extends Mailable
{
    protected array $mailData;
    protected string $template;
    protected string $subject;

    public function __construct(array $mailData, string $template, string $subject)
    {
        $this->mailData = $mailData;
        $this->template = $template;
        $this->subject = $subject;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->template,
            with: $this->mailData
        );
    }
}

/**
 * Multiple Alerts Email Mailable
 */
class MultipleAlertsEmailMailable extends Mailable
{
    protected array $mailData;
    protected string $template;
    protected string $subject;

    public function __construct(array $mailData, string $template, string $subject)
    {
        $this->mailData = $mailData;
        $this->template = $template;
        $this->subject = $subject;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->template,
            with: $this->mailData
        );
    }
}

/**
 * Alert Summary Email Mailable
 */
class AlertSummaryEmailMailable extends Mailable
{
    protected array $mailData;
    protected string $template;
    protected string $subject;

    public function __construct(array $mailData, string $template, string $subject)
    {
        $this->mailData = $mailData;
        $this->template = $template;
        $this->subject = $subject;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->template,
            with: $this->mailData
        );
    }
}
