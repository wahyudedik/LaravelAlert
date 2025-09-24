<?php

namespace Wahyudedik\LaravelAlert\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class StatusCommand extends Command
{
    protected $signature = 'laravel-alert:status 
                            {--json : Output as JSON}
                            {--verbose : Show detailed information}';

    protected $description = 'Show Laravel Alert package status and information';

    public function handle()
    {
        if ($this->option('json')) {
            $this->outputJson();
        } else {
            $this->outputTable();
        }

        return 0;
    }

    protected function outputTable(): void
    {
        $this->info('📊 Laravel Alert Status');
        $this->newLine();

        // Package Information
        $this->line('📦 <comment>Package Information:</comment>');
        $this->table(
            ['Property', 'Value'],
            [
                ['Name', 'wahyudedik/laravel-alert'],
                ['Version', '1.0.0'],
                ['Description', 'A comprehensive Laravel alert system'],
                ['Author', 'Wahyudedik'],
                ['License', 'MIT'],
                ['Homepage', 'https://github.com/wahyudedik/LaravelAlert'],
            ]
        );

        // Installation Status
        $this->line('🔧 <comment>Installation Status:</comment>');
        $this->table(
            ['Component', 'Status', 'Path'],
            [
                ['Configuration', $this->getConfigStatus(), $this->getConfigPath()],
                ['Views', $this->getViewsStatus(), $this->getViewsPath()],
                ['Assets', $this->getAssetsStatus(), $this->getAssetsPath()],
                ['Migrations', $this->getMigrationsStatus(), $this->getMigrationsPath()],
                ['Translations', $this->getTranslationsStatus(), $this->getTranslationsPath()],
            ]
        );

        // Configuration
        $this->line('⚙️  <comment>Configuration:</comment>');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Theme', Config::get('laravel-alert.theme', 'bootstrap')],
                ['Position', Config::get('laravel-alert.position', 'top-right')],
                ['Dismissible', Config::get('laravel-alert.dismissible', true) ? 'Yes' : 'No'],
                ['Auto Dismiss', Config::get('laravel-alert.auto_dismiss', true) ? 'Yes' : 'No'],
                ['Auto Dismiss Delay', Config::get('laravel-alert.auto_dismiss_delay', 5000) . 'ms'],
                ['Storage Driver', Config::get('laravel-alert.storage.driver', 'session')],
                ['Cache Enabled', Config::get('laravel-alert.cache.enabled', false) ? 'Yes' : 'No'],
                ['Redis Enabled', Config::get('laravel-alert.redis.enabled', false) ? 'Yes' : 'No'],
                ['Pusher Enabled', Config::get('laravel-alert.pusher.enabled', false) ? 'Yes' : 'No'],
                ['WebSocket Enabled', Config::get('laravel-alert.websocket.enabled', false) ? 'Yes' : 'No'],
                ['Email Enabled', Config::get('laravel-alert.email.enabled', false) ? 'Yes' : 'No'],
            ]
        );

        if ($this->option('verbose')) {
            // Storage Statistics
            $this->line('📊 <comment>Storage Statistics:</comment>');
            $this->table(
                ['Storage Type', 'Count', 'Size'],
                [
                    ['Cache', $this->getCacheCount(), $this->getCacheSize()],
                    ['Session', $this->getSessionCount(), $this->getSessionSize()],
                    ['Database', $this->getDatabaseCount(), $this->getDatabaseSize()],
                    ['Redis', $this->getRedisCount(), $this->getRedisSize()],
                ]
            );

            // Performance
            $this->line('⚡ <comment>Performance:</comment>');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Memory Usage', $this->getMemoryUsage()],
                    ['Execution Time', $this->getExecutionTime()],
                    ['Cache Hit Rate', $this->getCacheHitRate()],
                    ['Database Queries', $this->getDatabaseQueries()],
                ]
            );
        }

        $this->newLine();
        $this->line('💡 <comment>Useful Commands:</comment>');
        $this->line('   <comment>php artisan laravel-alert:install</comment> - Install package');
        $this->line('   <comment>php artisan laravel-alert:publish</comment> - Publish assets');
        $this->line('   <comment>php artisan laravel-alert:clear</comment> - Clear data');
        $this->line('   <comment>php artisan laravel-alert:status --verbose</comment> - Detailed status');
    }

    protected function outputJson(): void
    {
        $status = [
            'package' => [
                'name' => 'wahyudedik/laravel-alert',
                'version' => '1.0.0',
                'description' => 'A comprehensive Laravel alert system',
                'author' => 'Wahyudedik',
                'license' => 'MIT',
                'homepage' => 'https://github.com/wahyudedik/LaravelAlert',
            ],
            'installation' => [
                'config' => $this->getConfigStatus(),
                'views' => $this->getViewsStatus(),
                'assets' => $this->getAssetsStatus(),
                'migrations' => $this->getMigrationsStatus(),
                'translations' => $this->getTranslationsStatus(),
            ],
            'configuration' => [
                'theme' => Config::get('laravel-alert.theme', 'bootstrap'),
                'position' => Config::get('laravel-alert.position', 'top-right'),
                'dismissible' => Config::get('laravel-alert.dismissible', true),
                'auto_dismiss' => Config::get('laravel-alert.auto_dismiss', true),
                'auto_dismiss_delay' => Config::get('laravel-alert.auto_dismiss_delay', 5000),
                'storage_driver' => Config::get('laravel-alert.storage.driver', 'session'),
                'cache_enabled' => Config::get('laravel-alert.cache.enabled', false),
                'redis_enabled' => Config::get('laravel-alert.redis.enabled', false),
                'pusher_enabled' => Config::get('laravel-alert.pusher.enabled', false),
                'websocket_enabled' => Config::get('laravel-alert.websocket.enabled', false),
                'email_enabled' => Config::get('laravel-alert.email.enabled', false),
            ],
        ];

        if ($this->option('verbose')) {
            $status['storage'] = [
                'cache' => ['count' => $this->getCacheCount(), 'size' => $this->getCacheSize()],
                'session' => ['count' => $this->getSessionCount(), 'size' => $this->getSessionSize()],
                'database' => ['count' => $this->getDatabaseCount(), 'size' => $this->getDatabaseSize()],
                'redis' => ['count' => $this->getRedisCount(), 'size' => $this->getRedisSize()],
            ];

            $status['performance'] = [
                'memory_usage' => $this->getMemoryUsage(),
                'execution_time' => $this->getExecutionTime(),
                'cache_hit_rate' => $this->getCacheHitRate(),
                'database_queries' => $this->getDatabaseQueries(),
            ];
        }

        $this->line(json_encode($status, JSON_PRETTY_PRINT));
    }

    protected function getConfigStatus(): string
    {
        return File::exists(config_path('laravel-alert.php')) ? '✅ Installed' : '❌ Not installed';
    }

    protected function getConfigPath(): string
    {
        return config_path('laravel-alert.php');
    }

    protected function getViewsStatus(): string
    {
        return File::exists(resource_path('views/vendor/laravel-alert')) ? '✅ Installed' : '❌ Not installed';
    }

    protected function getViewsPath(): string
    {
        return resource_path('views/vendor/laravel-alert');
    }

    protected function getAssetsStatus(): string
    {
        return File::exists(public_path('css/laravel-alert')) ? '✅ Installed' : '❌ Not installed';
    }

    protected function getAssetsPath(): string
    {
        return public_path('css/laravel-alert');
    }

    protected function getMigrationsStatus(): string
    {
        $migrations = glob(database_path('migrations/*_create_alerts_table.php'));
        return !empty($migrations) ? '✅ Installed' : '❌ Not installed';
    }

    protected function getMigrationsPath(): string
    {
        return database_path('migrations');
    }

    protected function getTranslationsStatus(): string
    {
        return File::exists(resource_path('lang/vendor/laravel-alert')) ? '✅ Installed' : '❌ Not installed';
    }

    protected function getTranslationsPath(): string
    {
        return resource_path('lang/vendor/laravel-alert');
    }

    protected function getCacheCount(): int
    {
        try {
            $keys = Cache::getStore()->connection()->keys('laravel_alert_*');
            return count($keys);
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getCacheSize(): string
    {
        try {
            $keys = Cache::getStore()->connection()->keys('laravel_alert_*');
            $size = 0;
            foreach ($keys as $key) {
                $size += strlen(Cache::getStore()->connection()->get($key));
            }
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return '0 B';
        }
    }

    protected function getSessionCount(): int
    {
        try {
            $sessions = glob(storage_path('framework/sessions/*'));
            return count($sessions);
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getSessionSize(): string
    {
        try {
            $sessions = glob(storage_path('framework/sessions/*'));
            $size = 0;
            foreach ($sessions as $session) {
                $size += filesize($session);
            }
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return '0 B';
        }
    }

    protected function getDatabaseCount(): int
    {
        try {
            if (config('laravel-alert.storage.driver') === 'database') {
                return DB::table('alerts')->count();
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getDatabaseSize(): string
    {
        try {
            if (config('laravel-alert.storage.driver') === 'database') {
                $result = DB::select("SELECT pg_size_pretty(pg_total_relation_size('alerts')) as size");
                return $result[0]->size ?? '0 B';
            }
            return '0 B';
        } catch (\Exception $e) {
            return '0 B';
        }
    }

    protected function getRedisCount(): int
    {
        try {
            if (config('laravel-alert.storage.driver') === 'redis') {
                $keys = Redis::keys('laravel_alert_*');
                return count($keys);
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getRedisSize(): string
    {
        try {
            if (config('laravel-alert.storage.driver') === 'redis') {
                $keys = Redis::keys('laravel_alert_*');
                $size = 0;
                foreach ($keys as $key) {
                    $size += strlen(Redis::get($key));
                }
                return $this->formatBytes($size);
            }
            return '0 B';
        } catch (\Exception $e) {
            return '0 B';
        }
    }

    protected function getMemoryUsage(): string
    {
        return $this->formatBytes(memory_get_usage(true));
    }

    protected function getExecutionTime(): string
    {
        return round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4) . 's';
    }

    protected function getCacheHitRate(): string
    {
        try {
            $hits = Cache::getStore()->getStats()['hits'] ?? 0;
            $misses = Cache::getStore()->getStats()['misses'] ?? 0;
            $total = $hits + $misses;
            return $total > 0 ? round(($hits / $total) * 100, 2) . '%' : '0%';
        } catch (\Exception $e) {
            return '0%';
        }
    }

    protected function getDatabaseQueries(): int
    {
        try {
            return count(DB::getQueryLog());
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
