<?php

namespace Wahyudedik\LaravelAlert\Managers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceOptimizer
{
    protected array $config;
    protected array $metrics;
    protected array $optimizations;

    public function __construct()
    {
        $this->config = config('laravel-alert', []);
        $this->metrics = [];
        $this->optimizations = [];
        $this->initializeOptimizations();
    }

    /**
     * Initialize performance optimizations.
     */
    protected function initializeOptimizations(): void
    {
        $this->optimizations = [
            'batch_processing' => $this->config['batch_processing'] ?? true,
            'lazy_loading' => $this->config['lazy_loading'] ?? true,
            'query_optimization' => $this->config['query_optimization'] ?? true,
            'cache_warming' => $this->config['cache_warming'] ?? true,
            'index_optimization' => $this->config['index_optimization'] ?? true,
            'memory_optimization' => $this->config['memory_optimization'] ?? true,
            'connection_pooling' => $this->config['connection_pooling'] ?? true,
            'compression' => $this->config['compression'] ?? true
        ];
    }

    /**
     * Optimize alert retrieval.
     */
    public function optimizeAlertRetrieval(array $alertIds, string $driver = 'database'): array
    {
        $startTime = microtime(true);

        switch ($driver) {
            case 'redis':
                $alerts = $this->optimizeRedisRetrieval($alertIds);
                break;
            case 'cache':
                $alerts = $this->optimizeCacheRetrieval($alertIds);
                break;
            case 'database':
            default:
                $alerts = $this->optimizeDatabaseRetrieval($alertIds);
                break;
        }

        $this->recordMetric('alert_retrieval_time', microtime(true) - $startTime);
        $this->recordMetric('alerts_retrieved', count($alerts));

        return $alerts;
    }

    /**
     * Optimize Redis retrieval.
     */
    protected function optimizeRedisRetrieval(array $alertIds): array
    {
        if (!$this->optimizations['batch_processing']) {
            return $this->getAlertsSequentially($alertIds, 'redis');
        }

        // Batch retrieve from Redis
        $pipeline = Redis::pipeline();
        foreach ($alertIds as $alertId) {
            $pipeline->get("laravel_alert:alert:{$alertId}");
        }
        $results = $pipeline->exec();

        $alerts = [];
        foreach ($results as $result) {
            if ($result) {
                $alert = json_decode($result, true);
                if ($alert && $this->isAlertValid($alert)) {
                    $alerts[] = $alert;
                }
            }
        }

        return $alerts;
    }

    /**
     * Optimize cache retrieval.
     */
    protected function optimizeCacheRetrieval(array $alertIds): array
    {
        if (!$this->optimizations['batch_processing']) {
            return $this->getAlertsSequentially($alertIds, 'cache');
        }

        // Batch retrieve from cache
        $cacheKeys = array_map(function ($id) {
            return "laravel_alert:alert:{$id}";
        }, $alertIds);

        $alerts = [];
        foreach ($cacheKeys as $key) {
            $alert = Cache::get($key);
            if ($alert && $this->isAlertValid($alert)) {
                $alerts[] = $alert;
            }
        }

        return $alerts;
    }

    /**
     * Optimize database retrieval.
     */
    protected function optimizeDatabaseRetrieval(array $alertIds): array
    {
        if (!$this->optimizations['query_optimization']) {
            return $this->getAlertsSequentially($alertIds, 'database');
        }

        // Single optimized query
        $query = DB::table('alerts')
            ->whereIn('id', $alertIds)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->whereNull('dismissed_at')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        if ($this->optimizations['lazy_loading']) {
            $query->select([
                'id',
                'type',
                'message',
                'title',
                'user_id',
                'session_id',
                'alert_type',
                'theme',
                'position',
                'animation',
                'dismissible',
                'auto_dismiss',
                'auto_dismiss_delay',
                'expires_at',
                'priority',
                'context',
                'field',
                'form',
                'icon',
                'class',
                'style',
                'html_content',
                'created_at',
                'updated_at'
            ]);
        }

        return $query->get()->toArray();
    }

    /**
     * Get alerts sequentially (fallback).
     */
    protected function getAlertsSequentially(array $alertIds, string $driver): array
    {
        $alerts = [];

        foreach ($alertIds as $alertId) {
            $alert = $this->getSingleAlert($alertId, $driver);
            if ($alert && $this->isAlertValid($alert)) {
                $alerts[] = $alert;
            }
        }

        return $alerts;
    }

    /**
     * Get single alert.
     */
    protected function getSingleAlert(string $alertId, string $driver): ?array
    {
        switch ($driver) {
            case 'redis':
                $data = Redis::get("laravel_alert:alert:{$alertId}");
                return $data ? json_decode($data, true) : null;
            case 'cache':
                return Cache::get("laravel_alert:alert:{$alertId}");
            case 'database':
            default:
                return DB::table('alerts')->where('id', $alertId)->first();
        }
    }

    /**
     * Optimize alert storage.
     */
    public function optimizeAlertStorage(array $alerts, string $driver = 'database'): bool
    {
        $startTime = microtime(true);

        switch ($driver) {
            case 'redis':
                $result = $this->optimizeRedisStorage($alerts);
                break;
            case 'cache':
                $result = $this->optimizeCacheStorage($alerts);
                break;
            case 'database':
            default:
                $result = $this->optimizeDatabaseStorage($alerts);
                break;
        }

        $this->recordMetric('alert_storage_time', microtime(true) - $startTime);
        $this->recordMetric('alerts_stored', count($alerts));

        return $result;
    }

    /**
     * Optimize Redis storage.
     */
    protected function optimizeRedisStorage(array $alerts): bool
    {
        if (!$this->optimizations['batch_processing']) {
            return $this->storeAlertsSequentially($alerts, 'redis');
        }

        // Batch store in Redis
        $pipeline = Redis::pipeline();
        foreach ($alerts as $alert) {
            $key = "laravel_alert:alert:{$alert['id']}";
            $ttl = $this->calculateTtl($alert);
            $pipeline->setex($key, $ttl, json_encode($alert));
        }

        $results = $pipeline->exec();
        return !in_array(false, $results);
    }

    /**
     * Optimize cache storage.
     */
    protected function optimizeCacheStorage(array $alerts): bool
    {
        if (!$this->optimizations['batch_processing']) {
            return $this->storeAlertsSequentially($alerts, 'cache');
        }

        // Batch store in cache
        foreach ($alerts as $alert) {
            $key = "laravel_alert:alert:{$alert['id']}";
            $ttl = $this->calculateTtl($alert);
            Cache::put($key, $alert, $ttl);
        }

        return true;
    }

    /**
     * Optimize database storage.
     */
    protected function optimizeDatabaseStorage(array $alerts): bool
    {
        if (!$this->optimizations['batch_processing']) {
            return $this->storeAlertsSequentially($alerts, 'database');
        }

        // Batch insert into database
        try {
            DB::table('alerts')->insert($alerts);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to batch insert alerts: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Store alerts sequentially (fallback).
     */
    protected function storeAlertsSequentially(array $alerts, string $driver): bool
    {
        foreach ($alerts as $alert) {
            if (!$this->storeSingleAlert($alert, $driver)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Store single alert.
     */
    protected function storeSingleAlert(array $alert, string $driver): bool
    {
        try {
            switch ($driver) {
                case 'redis':
                    $key = "laravel_alert:alert:{$alert['id']}";
                    $ttl = $this->calculateTtl($alert);
                    return Redis::setex($key, $ttl, json_encode($alert));
                case 'cache':
                    $key = "laravel_alert:alert:{$alert['id']}";
                    $ttl = $this->calculateTtl($alert);
                    Cache::put($key, $alert, $ttl);
                    return true;
                case 'database':
                default:
                    return DB::table('alerts')->insert($alert);
            }
        } catch (\Exception $e) {
            Log::error('Failed to store alert: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Optimize database indexes.
     */
    public function optimizeIndexes(): bool
    {
        if (!$this->optimizations['index_optimization']) {
            return true;
        }

        try {
            // Analyze table for better query planning
            DB::statement('ANALYZE TABLE alerts');

            // Optimize indexes
            $this->createOptimalIndexes();

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to optimize indexes: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create optimal indexes.
     */
    protected function createOptimalIndexes(): void
    {
        $indexes = [
            'idx_alerts_user_active' => ['user_id', 'is_active'],
            'idx_alerts_session_active' => ['session_id', 'is_active'],
            'idx_alerts_type_active' => ['type', 'is_active'],
            'idx_alerts_priority_created' => ['priority', 'created_at'],
            'idx_alerts_expires' => ['expires_at'],
            'idx_alerts_dismissed' => ['dismissed_at'],
            'idx_alerts_read' => ['read_at']
        ];

        foreach ($indexes as $indexName => $columns) {
            try {
                $columnsStr = implode(', ', $columns);
                DB::statement("CREATE INDEX IF NOT EXISTS {$indexName} ON alerts ({$columnsStr})");
            } catch (\Exception $e) {
                Log::warning("Failed to create index {$indexName}: " . $e->getMessage());
            }
        }
    }

    /**
     * Warm up cache.
     */
    public function warmUpCache(): bool
    {
        if (!$this->optimizations['cache_warming']) {
            return true;
        }

        try {
            // Pre-load frequently accessed alerts
            $frequentAlerts = $this->getFrequentAlerts();

            foreach ($frequentAlerts as $alert) {
                $key = "laravel_alert:alert:{$alert->id}";
                Cache::put($key, $alert, 3600);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to warm up cache: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get frequently accessed alerts.
     */
    protected function getFrequentAlerts(): array
    {
        return DB::table('alerts')
            ->where('is_active', true)
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->toArray();
    }

    /**
     * Optimize memory usage.
     */
    public function optimizeMemory(): bool
    {
        if (!$this->optimizations['memory_optimization']) {
            return true;
        }

        try {
            // Clear unused cache entries
            $this->clearUnusedCache();

            // Optimize garbage collection
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to optimize memory: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear unused cache entries.
     */
    protected function clearUnusedCache(): void
    {
        // Clear expired cache entries
        $pattern = 'laravel_alert:*';
        $keys = Cache::getRedis()->keys($pattern);

        foreach ($keys as $key) {
            $ttl = Cache::getRedis()->ttl($key);
            if ($ttl === -2) { // Key doesn't exist
                Cache::forget($key);
            }
        }
    }

    /**
     * Optimize connection pooling.
     */
    public function optimizeConnections(): bool
    {
        if (!$this->optimizations['connection_pooling']) {
            return true;
        }

        try {
            // Optimize database connections
            DB::connection()->getPdo()->setAttribute(\PDO::ATTR_PERSISTENT, true);

            // Optimize Redis connections
            if (class_exists('Redis')) {
                Redis::connection()->setOption(\Redis::OPT_TCP_KEEPALIVE, 1);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to optimize connections: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enable compression.
     */
    public function enableCompression(): bool
    {
        if (!$this->optimizations['compression']) {
            return true;
        }

        try {
            // Enable Redis compression
            if (class_exists('Redis')) {
                Redis::connection()->setOption(\Redis::OPT_COMPRESSION, \Redis::COMPRESSION_LZ4);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to enable compression: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get performance metrics.
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    /**
     * Record a metric.
     */
    protected function recordMetric(string $name, $value): void
    {
        $this->metrics[$name] = $value;
    }

    /**
     * Get optimization status.
     */
    public function getOptimizationStatus(): array
    {
        return [
            'optimizations' => $this->optimizations,
            'metrics' => $this->metrics,
            'recommendations' => $this->getRecommendations()
        ];
    }

    /**
     * Get performance recommendations.
     */
    protected function getRecommendations(): array
    {
        $recommendations = [];

        if ($this->metrics['alert_retrieval_time'] > 0.1) {
            $recommendations[] = 'Consider enabling batch processing for alert retrieval';
        }

        if ($this->metrics['alert_storage_time'] > 0.05) {
            $recommendations[] = 'Consider enabling batch processing for alert storage';
        }

        if ($this->metrics['alerts_retrieved'] > 100) {
            $recommendations[] = 'Consider implementing pagination for large alert sets';
        }

        return $recommendations;
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
    protected function calculateTtl(array $alert): int
    {
        if ($alert['expires_at']) {
            return $alert['expires_at'] - time();
        }

        return 3600; // 1 hour default
    }

    /**
     * Run all optimizations.
     */
    public function runAllOptimizations(): bool
    {
        $results = [];

        $results['indexes'] = $this->optimizeIndexes();
        $results['cache'] = $this->warmUpCache();
        $results['memory'] = $this->optimizeMemory();
        $results['connections'] = $this->optimizeConnections();
        $results['compression'] = $this->enableCompression();

        return !in_array(false, $results);
    }

    /**
     * Get performance report.
     */
    public function getPerformanceReport(): array
    {
        return [
            'optimization_status' => $this->getOptimizationStatus(),
            'metrics' => $this->getMetrics(),
            'recommendations' => $this->getRecommendations(),
            'timestamp' => now()
        ];
    }
}
