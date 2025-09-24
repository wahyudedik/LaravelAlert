<?php

namespace Wahyudedik\LaravelAlert\Managers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface;

class CacheAlertManager implements AlertManagerInterface
{
    protected array $config;
    protected string $prefix;
    protected int $defaultTtl;
    protected string $cacheDriver;

    public function __construct()
    {
        $this->config = config('laravel-alert', []);
        $this->prefix = $this->config['cache_prefix'] ?? 'laravel_alert';
        $this->defaultTtl = $this->config['cache_ttl'] ?? 3600; // 1 hour default
        $this->cacheDriver = $this->config['cache_driver'] ?? 'file';
    }

    /**
     * Add a success alert.
     */
    public function success(string $message, ?string $title = null, array $options = []): self
    {
        return $this->add('success', $message, $title, $options);
    }

    /**
     * Add an error alert.
     */
    public function error(string $message, ?string $title = null, array $options = []): self
    {
        return $this->add('error', $message, $title, $options);
    }

    /**
     * Add a warning alert.
     */
    public function warning(string $message, ?string $title = null, array $options = []): self
    {
        return $this->add('warning', $message, $title, $options);
    }

    /**
     * Add an info alert.
     */
    public function info(string $message, ?string $title = null, array $options = []): self
    {
        return $this->add('info', $message, $title, $options);
    }

    /**
     * Add an alert with custom type.
     */
    public function add(string $type, string $message, ?string $title = null, array $options = []): self
    {
        $alertId = $this->generateAlertId();
        $userId = Auth::id();
        $sessionId = Session::getId();

        $alertData = [
            'id' => $alertId,
            'type' => $type,
            'message' => $message,
            'title' => $title,
            'user_id' => $userId,
            'session_id' => $sessionId,
            'alert_type' => $options['alert_type'] ?? 'alert',
            'theme' => $options['theme'] ?? $this->config['default_theme'] ?? 'bootstrap',
            'position' => $options['position'] ?? $this->config['position'] ?? 'top-right',
            'animation' => $options['animation'] ?? $this->config['animation'] ?? 'fade',
            'dismissible' => $options['dismissible'] ?? true,
            'auto_dismiss' => $options['auto_dismiss'] ?? false,
            'auto_dismiss_delay' => $options['auto_dismiss_delay'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
            'priority' => $options['priority'] ?? 0,
            'context' => $options['context'] ?? null,
            'field' => $options['field'] ?? null,
            'form' => $options['form'] ?? null,
            'icon' => $options['icon'] ?? null,
            'class' => $options['class'] ?? null,
            'style' => $options['style'] ?? null,
            'html_content' => $options['html_content'] ?? null,
            'data_attributes' => $options['data_attributes'] ?? null,
            'options' => $options,
            'created_at' => time(),
            'updated_at' => time(),
            'is_active' => true,
            'dismissed_at' => null,
            'read_at' => null
        ];

        // Store alert in cache
        $this->storeAlert($alertId, $alertData);

        // Add to user/session index
        $this->addToIndex($userId, $sessionId, $alertId);

        // Add to type index
        $this->addToTypeIndex($type, $alertId);

        // Add to priority index
        if ($alertData['priority'] > 0) {
            $this->addToPriorityIndex($alertId);
        }

        return $this;
    }

    /**
     * Get all alerts.
     */
    public function getAlerts(): array
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        $alertIds = $this->getUserAlertIds($userId, $sessionId);
        $alerts = [];

        foreach ($alertIds as $alertId) {
            $alert = $this->getAlert($alertId);
            if ($alert && $this->isAlertValid($alert)) {
                $alerts[] = $alert;
            }
        }

        // Sort by priority and creation time
        usort($alerts, function ($a, $b) {
            if ($a['priority'] == $b['priority']) {
                return $b['created_at'] - $a['created_at'];
            }
            return $b['priority'] - $a['priority'];
        });

        return $alerts;
    }

    /**
     * Clear all alerts.
     */
    public function clear(): self
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        $alertIds = $this->getUserAlertIds($userId, $sessionId);

        foreach ($alertIds as $alertId) {
            $this->removeAlert($alertId);
        }

        return $this;
    }

    /**
     * Render a single alert.
     */
    public function render(string $type, string $message, ?string $title = null, array $options = []): string
    {
        $this->add($type, $message, $title, $options);
        $alerts = $this->getAlerts();
        $latestAlert = $alerts[0] ?? null;

        if (!$latestAlert) {
            return '';
        }

        return $this->renderAlert($latestAlert);
    }

    /**
     * Render all alerts.
     */
    public function renderAll(): string
    {
        $alerts = $this->getAlerts();
        $html = '';

        foreach ($alerts as $alert) {
            $html .= $this->renderAlert($alert);
        }

        return $html;
    }

    /**
     * Render a single alert object.
     */
    protected function renderAlert(array $alert): string
    {
        $theme = $alert['theme'] ?? 'bootstrap';
        $view = "laravel-alert::components.{$theme}.alert";

        return view($view, [
            'alert' => (object) $alert,
            'config' => $this->config,
        ])->render();
    }

    /**
     * Store alert in cache.
     */
    protected function storeAlert(string $alertId, array $alertData): void
    {
        $key = $this->getAlertKey($alertId);
        $ttl = $this->calculateTtl($alertData);

        Cache::driver($this->cacheDriver)->put($key, $alertData, $ttl);
    }

    /**
     * Get alert from cache.
     */
    protected function getAlert(string $alertId): ?array
    {
        $key = $this->getAlertKey($alertId);
        return Cache::driver($this->cacheDriver)->get($key);
    }

    /**
     * Remove alert from cache.
     */
    protected function removeAlert(string $alertId): void
    {
        $key = $this->getAlertKey($alertId);
        Cache::driver($this->cacheDriver)->forget($key);

        // Remove from indexes
        $this->removeFromIndexes($alertId);
    }

    /**
     * Add alert to user/session index.
     */
    protected function addToIndex(?string $userId, string $sessionId, string $alertId): void
    {
        if ($userId) {
            $key = $this->getUserIndexKey($userId);
            $alertIds = Cache::driver($this->cacheDriver)->get($key, []);
            $alertIds[] = $alertId;
            Cache::driver($this->cacheDriver)->put($key, array_unique($alertIds), $this->defaultTtl);
        }

        $key = $this->getSessionIndexKey($sessionId);
        $alertIds = Cache::driver($this->cacheDriver)->get($key, []);
        $alertIds[] = $alertId;
        Cache::driver($this->cacheDriver)->put($key, array_unique($alertIds), $this->defaultTtl);
    }

    /**
     * Add alert to type index.
     */
    protected function addToTypeIndex(string $type, string $alertId): void
    {
        $key = $this->getTypeIndexKey($type);
        $alertIds = Cache::driver($this->cacheDriver)->get($key, []);
        $alertIds[] = $alertId;
        Cache::driver($this->cacheDriver)->put($key, array_unique($alertIds), $this->defaultTtl);
    }

    /**
     * Add alert to priority index.
     */
    protected function addToPriorityIndex(string $alertId): void
    {
        $key = $this->getPriorityIndexKey();
        $alertIds = Cache::driver($this->cacheDriver)->get($key, []);
        $alertIds[] = $alertId;
        Cache::driver($this->cacheDriver)->put($key, array_unique($alertIds), $this->defaultTtl);
    }

    /**
     * Get user alert IDs.
     */
    protected function getUserAlertIds(?string $userId, string $sessionId): array
    {
        $alertIds = [];

        if ($userId) {
            $userKey = $this->getUserIndexKey($userId);
            $userAlerts = Cache::driver($this->cacheDriver)->get($userKey, []);
            $alertIds = array_merge($alertIds, $userAlerts);
        }

        $sessionKey = $this->getSessionIndexKey($sessionId);
        $sessionAlerts = Cache::driver($this->cacheDriver)->get($sessionKey, []);
        $alertIds = array_merge($alertIds, $sessionAlerts);

        return array_unique($alertIds);
    }

    /**
     * Remove from all indexes.
     */
    protected function removeFromIndexes(string $alertId): void
    {
        // This is a simplified version - in a real implementation,
        // you would need to track and remove from all indexes
        // For now, we'll rely on TTL expiration
    }

    /**
     * Check if alert is valid.
     */
    protected function isAlertValid(array $alert): bool
    {
        if (!$alert['is_active']) {
            return false;
        }

        if ($alert['dismissed_at']) {
            return false;
        }

        if ($alert['expires_at'] && $alert['expires_at'] < time()) {
            return false;
        }

        return true;
    }

    /**
     * Calculate TTL for alert.
     */
    protected function calculateTtl(array $alertData): int
    {
        if ($alertData['expires_at']) {
            return $alertData['expires_at'] - time();
        }

        return $this->defaultTtl;
    }

    /**
     * Generate unique alert ID.
     */
    protected function generateAlertId(): string
    {
        return 'alert_' . time() . '_' . bin2hex(random_bytes(8));
    }

    /**
     * Get alert key.
     */
    protected function getAlertKey(string $alertId): string
    {
        return $this->prefix . ':alert:' . $alertId;
    }

    /**
     * Get user index key.
     */
    protected function getUserIndexKey(string $userId): string
    {
        return $this->prefix . ':user:' . $userId;
    }

    /**
     * Get session index key.
     */
    protected function getSessionIndexKey(string $sessionId): string
    {
        return $this->prefix . ':session:' . $sessionId;
    }

    /**
     * Get type index key.
     */
    protected function getTypeIndexKey(string $type): string
    {
        return $this->prefix . ':type:' . $type;
    }

    /**
     * Get priority index key.
     */
    protected function getPriorityIndexKey(): string
    {
        return $this->prefix . ':priority';
    }

    /**
     * Get alerts count.
     */
    public function count(): int
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        $alertIds = $this->getUserAlertIds($userId, $sessionId);
        $count = 0;

        foreach ($alertIds as $alertId) {
            $alert = $this->getAlert($alertId);
            if ($alert && $this->isAlertValid($alert)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Check if there are any alerts.
     */
    public function hasAlerts(): bool
    {
        return $this->count() > 0;
    }

    /**
     * Get alerts by type.
     */
    public function getAlertsByType(string $type): array
    {
        $typeKey = $this->getTypeIndexKey($type);
        $alertIds = Cache::driver($this->cacheDriver)->get($typeKey, []);
        $alerts = [];

        foreach ($alertIds as $alertId) {
            $alert = $this->getAlert($alertId);
            if ($alert && $this->isAlertValid($alert)) {
                $alerts[] = $alert;
            }
        }

        return $alerts;
    }

    /**
     * Clear alerts by type.
     */
    public function clearByType(string $type): self
    {
        $typeKey = $this->getTypeIndexKey($type);
        $alertIds = Cache::driver($this->cacheDriver)->get($typeKey, []);

        foreach ($alertIds as $alertId) {
            $this->removeAlert($alertId);
        }

        return $this;
    }

    /**
     * Add multiple alerts at once.
     */
    public function addMultiple(array $alerts): self
    {
        foreach ($alerts as $alert) {
            if (is_array($alert) && isset($alert['type'], $alert['message'])) {
                $this->add(
                    $alert['type'],
                    $alert['message'],
                    $alert['title'] ?? null,
                    $alert['options'] ?? []
                );
            }
        }

        return $this;
    }

    /**
     * Get the first alert.
     */
    public function first(): ?array
    {
        $alerts = $this->getAlerts();
        return $alerts[0] ?? null;
    }

    /**
     * Get the last alert.
     */
    public function last(): ?array
    {
        $alerts = $this->getAlerts();
        return end($alerts) ?: null;
    }

    /**
     * Remove a specific alert by ID.
     */
    public function removeById(string $id): self
    {
        $this->removeAlert($id);
        return $this;
    }

    /**
     * Flush alerts (clear and return them).
     */
    public function flush(): array
    {
        $alerts = $this->getAlerts();
        $this->clear();
        return $alerts;
    }

    /**
     * Add alert with expiration time.
     */
    public function addWithExpiration(
        string $type,
        string $message,
        ?string $title = null,
        int $expiresInSeconds = 3600,
        array $options = []
    ): self {
        $options['expires_at'] = time() + $expiresInSeconds;
        return $this->add($type, $message, $title, $options);
    }

    /**
     * Add alert with auto-dismiss.
     */
    public function addWithAutoDismiss(
        string $type,
        string $message,
        ?string $title = null,
        int $autoDismissDelay = 5000,
        array $options = []
    ): self {
        $options['auto_dismiss'] = true;
        $options['auto_dismiss_delay'] = $autoDismissDelay;
        return $this->add($type, $message, $title, $options);
    }

    /**
     * Add temporary alert (expires in specified seconds).
     */
    public function temporary(
        string $type,
        string $message,
        ?string $title = null,
        int $expiresInSeconds = 300,
        array $options = []
    ): self {
        return $this->addWithExpiration($type, $message, $title, $expiresInSeconds, $options);
    }

    /**
     * Add flash alert (auto-dismisses after delay).
     */
    public function flash(
        string $type,
        string $message,
        ?string $title = null,
        int $autoDismissDelay = 3000,
        array $options = []
    ): self {
        return $this->addWithAutoDismiss($type, $message, $title, $autoDismissDelay, $options);
    }

    /**
     * Clean up expired alerts.
     */
    public function cleanupExpired(): self
    {
        // Cache will automatically expire based on TTL
        // This method is kept for compatibility
        return $this;
    }

    /**
     * Get expired alerts.
     */
    public function getExpiredAlerts(): array
    {
        // Cache doesn't provide a way to get expired items
        // This method is kept for compatibility
        return [];
    }

    /**
     * Get alerts that should auto-dismiss.
     */
    public function getAutoDismissAlerts(): array
    {
        $autoDismissAlerts = [];
        $userId = Auth::id();
        $sessionId = Session::getId();

        $alertIds = $this->getUserAlertIds($userId, $sessionId);

        foreach ($alertIds as $alertId) {
            $alert = $this->getAlert($alertId);
            if ($alert && $alert['auto_dismiss'] && $alert['auto_dismiss_delay']) {
                $autoDismissAlerts[] = $alert;
            }
        }

        return $autoDismissAlerts;
    }

    /**
     * Get cache statistics.
     */
    public function getStats(): array
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        $alertIds = $this->getUserAlertIds($userId, $sessionId);

        $stats = [
            'total_alerts' => count($alertIds),
            'cache_driver' => $this->cacheDriver,
            'by_type' => [],
            'by_priority' => 0,
            'auto_dismiss' => 0
        ];

        foreach ($alertIds as $alertId) {
            $alert = $this->getAlert($alertId);
            if ($alert) {
                $type = $alert['type'];
                $stats['by_type'][$type] = ($stats['by_type'][$type] ?? 0) + 1;

                if ($alert['priority'] > 0) {
                    $stats['by_priority']++;
                }

                if ($alert['auto_dismiss']) {
                    $stats['auto_dismiss']++;
                }
            }
        }

        return $stats;
    }

    /**
     * Clear all cache data.
     */
    public function clearAll(): self
    {
        Cache::driver($this->cacheDriver)->flush();
        return $this;
    }

    /**
     * Get cache driver.
     */
    public function getCacheDriver(): string
    {
        return $this->cacheDriver;
    }

    /**
     * Set cache driver.
     */
    public function setCacheDriver(string $driver): self
    {
        $this->cacheDriver = $driver;
        return $this;
    }

    /**
     * Get cache TTL.
     */
    public function getTtl(): int
    {
        return $this->defaultTtl;
    }

    /**
     * Set cache TTL.
     */
    public function setTtl(int $ttl): self
    {
        $this->defaultTtl = $ttl;
        return $this;
    }
}
