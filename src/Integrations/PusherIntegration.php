<?php

namespace Wahyudedik\LaravelAlert\Integrations;

use Pusher\Pusher;
use Pusher\PusherException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface;

class PusherIntegration
{
    protected Pusher $pusher;
    protected array $config;
    protected bool $enabled;

    public function __construct()
    {
        $this->config = config('laravel-alert.pusher', []);
        $this->enabled = $this->config['enabled'] ?? false;

        if ($this->enabled) {
            $this->initializePusher();
        }
    }

    /**
     * Initialize Pusher client.
     */
    protected function initializePusher(): void
    {
        try {
            $this->pusher = new Pusher(
                $this->config['key'],
                $this->config['secret'],
                $this->config['app_id'],
                [
                    'cluster' => $this->config['cluster'],
                    'useTLS' => $this->config['use_tls'] ?? true,
                    'encrypted' => $this->config['encrypted'] ?? true,
                    'host' => $this->config['host'] ?? null,
                    'port' => $this->config['port'] ?? null,
                    'scheme' => $this->config['scheme'] ?? 'https',
                    'timeout' => $this->config['timeout'] ?? 30,
                    'curl_options' => $this->config['curl_options'] ?? []
                ]
            );
        } catch (PusherException $e) {
            Log::error('Failed to initialize Pusher: ' . $e->getMessage());
            $this->enabled = false;
        }
    }

    /**
     * Broadcast alert to Pusher.
     */
    public function broadcastAlert(array $alert, string $channel = null, array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $channel = $channel ?? $this->getDefaultChannel();
            $event = $options['event'] ?? 'alert.created';

            $data = [
                'alert' => $alert,
                'timestamp' => now()->toISOString(),
                'user_id' => $options['user_id'] ?? null,
                'session_id' => $options['session_id'] ?? null,
                'context' => $options['context'] ?? null
            ];

            $result = $this->pusher->trigger($channel, $event, $data);

            if ($result) {
                Log::info('Alert broadcasted to Pusher', [
                    'channel' => $channel,
                    'event' => $event,
                    'alert_id' => $alert['id'] ?? null
                ]);
            }

            return $result;
        } catch (PusherException $e) {
            Log::error('Failed to broadcast alert to Pusher: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast alert update.
     */
    public function broadcastAlertUpdate(array $alert, string $channel = null, array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $channel = $channel ?? $this->getDefaultChannel();
            $event = $options['event'] ?? 'alert.updated';

            $data = [
                'alert' => $alert,
                'timestamp' => now()->toISOString(),
                'user_id' => $options['user_id'] ?? null,
                'session_id' => $options['session_id'] ?? null,
                'context' => $options['context'] ?? null
            ];

            return $this->pusher->trigger($channel, $event, $data);
        } catch (PusherException $e) {
            Log::error('Failed to broadcast alert update to Pusher: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast alert dismissal.
     */
    public function broadcastAlertDismissal(string $alertId, string $channel = null, array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $channel = $channel ?? $this->getDefaultChannel();
            $event = $options['event'] ?? 'alert.dismissed';

            $data = [
                'alert_id' => $alertId,
                'timestamp' => now()->toISOString(),
                'user_id' => $options['user_id'] ?? null,
                'session_id' => $options['session_id'] ?? null,
                'reason' => $options['reason'] ?? 'user_dismissed'
            ];

            return $this->pusher->trigger($channel, $event, $data);
        } catch (PusherException $e) {
            Log::error('Failed to broadcast alert dismissal to Pusher: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast multiple alerts.
     */
    public function broadcastMultipleAlerts(array $alerts, string $channel = null, array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $channel = $channel ?? $this->getDefaultChannel();
            $event = $options['event'] ?? 'alerts.bulk_created';

            $data = [
                'alerts' => $alerts,
                'count' => count($alerts),
                'timestamp' => now()->toISOString(),
                'user_id' => $options['user_id'] ?? null,
                'session_id' => $options['session_id'] ?? null,
                'context' => $options['context'] ?? null
            ];

            return $this->pusher->trigger($channel, $event, $data);
        } catch (PusherException $e) {
            Log::error('Failed to broadcast multiple alerts to Pusher: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast alert to specific user.
     */
    public function broadcastToUser(int $userId, array $alert, string $event = 'alert.created'): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $channel = $this->getUserChannel($userId);

            $data = [
                'alert' => $alert,
                'timestamp' => now()->toISOString(),
                'user_id' => $userId
            ];

            return $this->pusher->trigger($channel, $event, $data);
        } catch (PusherException $e) {
            Log::error('Failed to broadcast alert to user via Pusher: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast alert to specific session.
     */
    public function broadcastToSession(string $sessionId, array $alert, string $event = 'alert.created'): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $channel = $this->getSessionChannel($sessionId);

            $data = [
                'alert' => $alert,
                'timestamp' => now()->toISOString(),
                'session_id' => $sessionId
            ];

            return $this->pusher->trigger($channel, $event, $data);
        } catch (PusherException $e) {
            Log::error('Failed to broadcast alert to session via Pusher: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast to multiple channels.
     */
    public function broadcastToChannels(array $channels, array $alert, string $event = 'alert.created'): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $data = [
                'alert' => $alert,
                'timestamp' => now()->toISOString()
            ];

            $results = [];
            foreach ($channels as $channel) {
                $results[] = $this->pusher->trigger($channel, $event, $data);
            }

            return !in_array(false, $results);
        } catch (PusherException $e) {
            Log::error('Failed to broadcast alert to multiple channels via Pusher: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get channel authentication.
     */
    public function authenticateChannel(string $channel, string $socketId, array $options = []): array
    {
        if (!$this->enabled) {
            return ['auth' => false];
        }

        try {
            $auth = $this->pusher->socket_auth($channel, $socketId, $options);
            return ['auth' => $auth];
        } catch (PusherException $e) {
            Log::error('Failed to authenticate Pusher channel: ' . $e->getMessage());
            return ['auth' => false];
        }
    }

    /**
     * Get channel presence information.
     */
    public function getChannelPresence(string $channel): array
    {
        if (!$this->enabled) {
            return [];
        }

        try {
            $response = $this->pusher->get('/channels/' . $channel . '/users');
            return $response['users'] ?? [];
        } catch (PusherException $e) {
            Log::error('Failed to get Pusher channel presence: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get default channel.
     */
    protected function getDefaultChannel(): string
    {
        return $this->config['default_channel'] ?? 'alerts';
    }

    /**
     * Get user-specific channel.
     */
    protected function getUserChannel(int $userId): string
    {
        $prefix = $this->config['user_channel_prefix'] ?? 'user-alerts';
        return $prefix . '-' . $userId;
    }

    /**
     * Get session-specific channel.
     */
    protected function getSessionChannel(string $sessionId): string
    {
        $prefix = $this->config['session_channel_prefix'] ?? 'session-alerts';
        return $prefix . '-' . $sessionId;
    }

    /**
     * Check if Pusher is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get Pusher configuration.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get Pusher client.
     */
    public function getPusher(): ?Pusher
    {
        return $this->pusher ?? null;
    }

    /**
     * Test Pusher connection.
     */
    public function testConnection(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $result = $this->pusher->trigger('test-channel', 'test-event', ['test' => true]);
            return $result !== false;
        } catch (PusherException $e) {
            Log::error('Pusher connection test failed: ' . $e->getMessage());
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
                'message' => 'Pusher integration is disabled'
            ];
        }

        try {
            $testResult = $this->testConnection();
            return [
                'enabled' => true,
                'status' => $testResult ? 'connected' : 'disconnected',
                'message' => $testResult ? 'Pusher connection successful' : 'Pusher connection failed',
                'config' => [
                    'app_id' => $this->config['app_id'],
                    'cluster' => $this->config['cluster'],
                    'use_tls' => $this->config['use_tls'] ?? true
                ]
            ];
        } catch (PusherException $e) {
            return [
                'enabled' => true,
                'status' => 'error',
                'message' => 'Pusher connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Enable Pusher integration.
     */
    public function enable(): void
    {
        $this->enabled = true;
        $this->initializePusher();
    }

    /**
     * Disable Pusher integration.
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Update Pusher configuration.
     */
    public function updateConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);

        if ($this->enabled) {
            $this->initializePusher();
        }
    }
}
