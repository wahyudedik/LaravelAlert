<?php

namespace Wahyudedik\LaravelAlert\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class ClearCommand extends Command
{
    protected $signature = 'laravel-alert:clear 
                            {--type=* : The type(s) to clear (cache, session, database, redis, all)}
                            {--force : Force clear without confirmation}
                            {--all : Clear all data}';

    protected $description = 'Clear Laravel Alert data from storage';

    protected $availableTypes = [
        'cache' => 'Clear alert cache',
        'session' => 'Clear alert sessions',
        'database' => 'Clear alert database records',
        'redis' => 'Clear alert Redis data',
        'all' => 'Clear all alert data'
    ];

    public function handle()
    {
        $this->info('🧹 Clearing Laravel Alert data...');
        $this->newLine();

        $types = $this->getTypesToClear();

        if (empty($types)) {
            $this->displayAvailableTypes();
            return 0;
        }

        if (!$this->option('force') && !$this->confirmClear($types)) {
            $this->info('Clear operation cancelled.');
            return 0;
        }

        foreach ($types as $type) {
            $this->clearType($type);
        }

        $this->displaySuccessMessage();
        return 0;
    }

    protected function getTypesToClear(): array
    {
        $types = $this->option('type');

        if ($this->option('all')) {
            return array_keys($this->availableTypes);
        }

        if (empty($types)) {
            $types = $this->askForTypes();
        }

        return array_filter($types, function ($type) {
            return array_key_exists($type, $this->availableTypes);
        });
    }

    protected function askForTypes(): array
    {
        $this->info('Available types:');
        foreach ($this->availableTypes as $type => $description) {
            $this->line("  <comment>{$type}</comment> - {$description}");
        }
        $this->newLine();

        $types = $this->ask('Which types would you like to clear? (comma-separated)', 'cache,session');

        return array_map('trim', explode(',', $types));
    }

    protected function confirmClear(array $types): bool
    {
        $this->warn('⚠️  This will permanently delete the following data:');
        foreach ($types as $type) {
            $this->line("   • {$this->availableTypes[$type]}");
        }
        $this->newLine();

        return $this->confirm('Are you sure you want to continue?');
    }

    protected function clearType(string $type): void
    {
        $description = $this->availableTypes[$type];
        $this->info("🗑️  Clearing {$type} ({$description})...");

        try {
            switch ($type) {
                case 'cache':
                    $this->clearCache();
                    break;
                case 'session':
                    $this->clearSession();
                    break;
                case 'database':
                    $this->clearDatabase();
                    break;
                case 'redis':
                    $this->clearRedis();
                    break;
                case 'all':
                    $this->clearAll();
                    break;
            }

            $this->line("   ✓ {$type} cleared successfully");
        } catch (\Exception $e) {
            $this->error("   ✗ Failed to clear {$type}: " . $e->getMessage());
        }
    }

    protected function clearCache(): void
    {
        // Clear Laravel cache
        Cache::flush();

        // Clear specific alert cache
        $cacheKeys = [
            'laravel_alert_*',
            'laravel_alert_alerts_*',
            'laravel_alert_stats_*',
            'laravel_alert_config_*',
        ];

        foreach ($cacheKeys as $pattern) {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->connection();
                $keys = $redis->keys($pattern);
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            }
        }
    }

    protected function clearSession(): void
    {
        // Clear session data
        Session::flush();

        // Clear specific alert sessions
        $sessionKeys = [
            'laravel_alerts',
            'laravel_alert_*',
        ];

        foreach ($sessionKeys as $key) {
            Session::forget($key);
        }
    }

    protected function clearDatabase(): void
    {
        // Clear database records
        if (config('laravel-alert.storage.driver') === 'database') {
            DB::table('alerts')->truncate();
            DB::table('alert_sessions')->truncate();
            DB::table('alert_statistics')->truncate();
        }
    }

    protected function clearRedis(): void
    {
        // Clear Redis data
        if (config('laravel-alert.storage.driver') === 'redis') {
            $redis = Redis::connection();
            $keys = $redis->keys('laravel_alert_*');
            if (!empty($keys)) {
                $redis->del($keys);
            }
        }
    }

    protected function clearAll(): void
    {
        $this->clearCache();
        $this->clearSession();
        $this->clearDatabase();
        $this->clearRedis();
    }

    protected function displayAvailableTypes(): void
    {
        $this->info('Available types:');
        $this->newLine();

        foreach ($this->availableTypes as $type => $description) {
            $this->line("  <comment>{$type}</comment> - {$description}");
        }

        $this->newLine();
        $this->line('Usage examples:');
        $this->line('  <comment>php artisan laravel-alert:clear --type=cache</comment>');
        $this->line('  <comment>php artisan laravel-alert:clear --type=cache,session</comment>');
        $this->line('  <comment>php artisan laravel-alert:clear --all</comment>');
        $this->line('  <comment>php artisan laravel-alert:clear --all --force</comment>');
    }

    protected function displaySuccessMessage(): void
    {
        $this->newLine();
        $this->info('🎉 Laravel Alert data cleared successfully!');
        $this->newLine();

        $this->line('📊 <comment>Cleared data:</comment>');

        $types = $this->getTypesToClear();

        if (in_array('cache', $types) || in_array('all', $types)) {
            $this->line('   • <comment>Cache</comment> - All cached alert data');
        }

        if (in_array('session', $types) || in_array('all', $types)) {
            $this->line('   • <comment>Session</comment> - All session alert data');
        }

        if (in_array('database', $types) || in_array('all', $types)) {
            $this->line('   • <comment>Database</comment> - All database alert records');
        }

        if (in_array('redis', $types) || in_array('all', $types)) {
            $this->line('   • <comment>Redis</comment> - All Redis alert data');
        }

        $this->newLine();
        $this->line('💡 <comment>Tips:</comment>');
        $this->line('   • Use <comment>--force</comment> to skip confirmation');
        $this->line('   • Use <comment>--all</comment> to clear everything');
        $this->line('   • Use <comment>--type=cache,session</comment> to clear specific types');
        $this->newLine();

        $this->line('🔄 <comment>Next steps:</comment>');
        $this->line('   • Run <comment>php artisan laravel-alert:install</comment> to reinstall');
        $this->line('   • Run <comment>php artisan laravel-alert:publish</comment> to republish assets');
        $this->newLine();

        $this->line('📖 <comment>Documentation:</comment>');
        $this->line('   <comment>https://wahyudedik.github.io/LaravelAlert</comment>');
    }
}
