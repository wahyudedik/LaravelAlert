<?php

namespace Wahyudedik\LaravelAlert\Integrations;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class WebSocketIntegration
{
    protected array $config;
    protected bool $enabled;
    protected string $driver;
    protected array $connections = [];

    public function __construct()
    {
        $this->config = config('laravel-alert.websocket', []);
        $this->enabled = $this->config['enabled'] ?? false;
        $this->driver = $this->config['driver'] ?? 'redis';
    }

    /**
     * Initialize WebSocket connection.
     */
    public function initialize(): void
    {
        if (!$this->enabled) {
            return;
        }

        try {
            switch ($this->driver) {
                case 'redis':
                    $this->initializeRedis();
                    break;
                case 'cache':
                    $this->initializeCache();
                    break;
                case 'database':
                    $this->initializeDatabase();
                    break;
                default:
                    Log::warning('Unknown WebSocket driver: ' . $this->driver);
            }
        } catch (\Exception $e) {
            Log::error('Failed to initialize WebSocket: ' . $e->getMessage());
            $this->enabled = false;
        }
    }

    /**
     * Initialize Redis WebSocket.
     */
    protected function initializeRedis(): void
    {
        if (!class_exists('Redis')) {
            throw new \Exception('Redis extension not available');
        }

        $this->connections['redis'] = Redis::connection();
    }

    /**
     * Initialize Cache WebSocket.
     */
    protected function initializeCache(): void
    {
        $this->connections['cache'] = Cache::store();
    }

    /**
     * Initialize Database WebSocket.
     */
    protected function initializeDatabase(): void
    {
        // Database WebSocket implementation
        $this->connections['database'] = true;
    }

    /**
     * Broadcast alert via WebSocket.
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
                'type' => 'alert',
                'event' => $event,
                'alert' => $alert,
                'timestamp' => now()->toISOString(),
                'user_id' => $options['user_id'] ?? null,
                'session_id' => $options['session_id'] ?? null,
                'context' => $options['context'] ?? null
            ];

            return $this->publishToChannel($channel, $data);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast alert via WebSocket: ' . $e->getMessage());
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
                'type' => 'alert_update',
                'event' => $event,
                'alert' => $alert,
                'timestamp' => now()->toISOString(),
                'user_id' => $options['user_id'] ?? null,
                'session_id' => $options['session_id'] ?? null
            ];

            return $this->publishToChannel($channel, $data);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast alert update via WebSocket: ' . $e->getMessage());
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
                'type' => 'alert_dismissal',
                'event' => $event,
                'alert_id' => $alertId,
                'timestamp' => now()->toISOString(),
                'user_id' => $options['user_id'] ?? null,
                'session_id' => $options['session_id'] ?? null,
                'reason' => $options['reason'] ?? 'user_dismissed'
            ];

            return $this->publishToChannel($channel, $data);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast alert dismissal via WebSocket: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast to specific user.
     */
    public function broadcastToUser(int $userId, array $alert, string $event = 'alert.created'): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $channel = $this->getUserChannel($userId);
            return $this->broadcastAlert($alert, $channel, ['event' => $event, 'user_id' => $userId]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast alert to user via WebSocket: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast to specific session.
     */
    public function broadcastToSession(string $sessionId, array $alert, string $event = 'alert.created'): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $channel = $this->getSessionChannel($sessionId);
            return $this->broadcastAlert($alert, $channel, ['event' => $event, 'session_id' => $sessionId]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast alert to session via WebSocket: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Subscribe to channel.
     */
    public function subscribeToChannel(string $channel, array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $subscription = [
                'channel' => $channel,
                'timestamp' => now()->toISOString(),
                'user_id' => $options['user_id'] ?? null,
                'session_id' => $options['session_id'] ?? null,
                'context' => $options['context'] ?? null
            ];

            return $this->storeSubscription($subscription);
        } catch (\Exception $e) {
            Log::error('Failed to subscribe to WebSocket channel: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Unsubscribe from channel.
     */
    public function unsubscribeFromChannel(string $channel, array $options = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            return $this->removeSubscription($channel, $options);
        } catch (\Exception $e) {
            Log::error('Failed to unsubscribe from WebSocket channel: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get active connections.
     */
    public function getActiveConnections(): array
    {
        if (!$this->enabled) {
            return [];
        }

        try {
            switch ($this->driver) {
                case 'redis':
                    return $this->getRedisConnections();
                case 'cache':
                    return $this->getCacheConnections();
                case 'database':
                    return $this->getDatabaseConnections();
                default:
                    return [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to get active WebSocket connections: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get Redis connections.
     */
    protected function getRedisConnections(): array
    {
        $pattern = $this->getConnectionKey('*');
        $keys = Redis::keys($pattern);

        $connections = [];
        foreach ($keys as $key) {
            $data = Redis::get($key);
            if ($data) {
                $connections[] = json_decode($data, true);
            }
        }

        return $connections;
    }

    /**
     * Get Cache connections.
     */
    protected function getCacheConnections(): array
    {
        $key = $this->getConnectionKey('active');
        return Cache::get($key, []);
    }

    /**
     * Get Database connections.
     */
    protected function getDatabaseConnections(): array
    {
        // Implement database connection tracking
        return [];
    }

    /**
     * Publish to channel.
     */
    protected function publishToChannel(string $channel, array $data): bool
    {
        try {
            switch ($this->driver) {
                case 'redis':
                    return $this->publishToRedis($channel, $data);
                case 'cache':
                    return $this->publishToCache($channel, $data);
                case 'database':
                    return $this->publishToDatabase($channel, $data);
                default:
                    return false;
            }
        } catch (\Exception $e) {
            Log::error('Failed to publish to WebSocket channel: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Publish to Redis.
     */
    protected function publishToRedis(string $channel, array $data): bool
    {
        $key = $this->getChannelKey($channel);
        $ttl = $this->config['ttl'] ?? 3600;

        return Redis::setex($key, $ttl, json_encode($data));
    }

    /**
     * Publish to Cache.
     */
    protected function publishToCache(string $channel, array $data): bool
    {
        $key = $this->getChannelKey($channel);
        $ttl = $this->config['ttl'] ?? 3600;

        Cache::put($key, $data, $ttl);
        return true;
    }

    /**
     * Publish to Database.
     */
    protected function publishToDatabase(string $channel, array $data): bool
    {
        // Implement database publishing
        return true;
    }

    /**
     * Store subscription.
     */
    protected function storeSubscription(array $subscription): bool
    {
        try {
            switch ($this->driver) {
                case 'redis':
                    return $this->storeRedisSubscription($subscription);
                case 'cache':
                    return $this->storeCacheSubscription($subscription);
                case 'database':
                    return $this->storeDatabaseSubscription($subscription);
                default:
                    return false;
            }
        } catch (\Exception $e) {
            Log::error('Failed to store WebSocket subscription: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Store Redis subscription.
     */
    protected function storeRedisSubscription(array $subscription): bool
    {
        $key = $this->getSubscriptionKey($subscription['channel']);
        $ttl = $this->config['subscription_ttl'] ?? 3600;

        return Redis::setex($key, $ttl, json_encode($subscription));
    }

    /**
     * Store Cache subscription.
     */
    protected function storeCacheSubscription(array $subscription): bool
    {
        $key = $this->getSubscriptionKey($subscription['channel']);
        $ttl = $this->config['subscription_ttl'] ?? 3600;

        Cache::put($key, $subscription, $ttl);
        return true;
    }

    /**
     * Store Database subscription.
     */
    protected function storeDatabaseSubscription(array $subscription): bool
    {
        // Implement database subscription storage
        return true;
    }

    /**
     * Remove subscription.
     */
    protected function removeSubscription(string $channel, array $options): bool
    {
        try {
            switch ($this->driver) {
                case 'redis':
                    return $this->removeRedisSubscription($channel, $options);
                case 'cache':
                    return $this->removeCacheSubscription($channel, $options);
                case 'database':
                    return $this->removeDatabaseSubscription($channel, $options);
                default:
                    return false;
            }
        } catch (\Exception $e) {
            Log::error('Failed to remove WebSocket subscription: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove Redis subscription.
     */
    protected function removeRedisSubscription(string $channel, array $options): bool
    {
        $key = $this->getSubscriptionKey($channel);
        return Redis::del($key) > 0;
    }

    /**
     * Remove Cache subscription.
     */
    protected function removeCacheSubscription(string $channel, array $options): bool
    {
        $key = $this->getSubscriptionKey($channel);
        return Cache::forget($key);
    }

    /**
     * Remove Database subscription.
     */
    protected function removeDatabaseSubscription(string $channel, array $options): bool
    {
        // Implement database subscription removal
        return true;
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
     * Get channel key.
     */
    protected function getChannelKey(string $channel): string
    {
        $prefix = $this->config['key_prefix'] ?? 'websocket';
        return $prefix . ':channel:' . $channel;
    }

    /**
     * Get subscription key.
     */
    protected function getSubscriptionKey(string $channel): string
    {
        $prefix = $this->config['key_prefix'] ?? 'websocket';
        return $prefix . ':subscription:' . $channel;
    }

    /**
     * Get connection key.
     */
    protected function getConnectionKey(string $identifier): string
    {
        $prefix = $this->config['key_prefix'] ?? 'websocket';
        return $prefix . ':connection:' . $identifier;
    }

    /**
     * Check if WebSocket is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get WebSocket configuration.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get WebSocket driver.
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * Test WebSocket connection.
     */
    public function testConnection(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $testData = [
                'type' => 'test',
                'event' => 'connection_test',
                'timestamp' => now()->toISOString()
            ];

            return $this->publishToChannel('test-channel', $testData);
        } catch (\Exception $e) {
            Log::error('WebSocket connection test failed: ' . $e->getMessage());
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
                'message' => 'WebSocket integration is disabled'
            ];
        }

        try {
            $testResult = $this->testConnection();
            return [
                'enabled' => true,
                'status' => $testResult ? 'connected' : 'disconnected',
                'message' => $testResult ? 'WebSocket connection successful' : 'WebSocket connection failed',
                'driver' => $this->driver,
                'config' => $this->config
            ];
        } catch (\Exception $e) {
            return [
                'enabled' => true,
                'status' => 'error',
                'message' => 'WebSocket connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Enable WebSocket integration.
     */
    public function enable(): void
    {
        $this->enabled = true;
        $this->initialize();
    }

    /**
     * Disable WebSocket integration.
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Update WebSocket configuration.
     */
    public function updateConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);

        if ($this->enabled) {
            $this->initialize();
        }
    }
}
