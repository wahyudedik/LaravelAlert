<?php

namespace Wahyudedik\LaravelAlert\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    protected $signature = 'laravel-alert:install 
                            {--force : Force the installation even if already installed}
                            {--no-interaction : Do not ask any interactive questions}';

    protected $description = 'Install Laravel Alert package and publish its assets';

    public function handle()
    {
        $this->info('🚀 Installing Laravel Alert...');
        $this->newLine();

        // Check if already installed
        if ($this->isAlreadyInstalled() && !$this->option('force')) {
            $this->warn('Laravel Alert is already installed!');

            if (!$this->option('no-interaction')) {
                if (!$this->confirm('Do you want to reinstall? This will overwrite existing files.')) {
                    $this->info('Installation cancelled.');
                    return 0;
                }
            }
        }

        // Run installation steps
        $this->publishConfiguration();
        $this->publishAssets();
        $this->publishViews();
        $this->publishMigrations();
        $this->publishTranslations();
        $this->runMigrations();
        $this->createDirectories();
        $this->updateComposerJson();
        $this->displaySuccessMessage();

        return 0;
    }

    protected function isAlreadyInstalled(): bool
    {
        return File::exists(config_path('laravel-alert.php'));
    }

    protected function publishConfiguration(): void
    {
        $this->info('📝 Publishing configuration...');

        try {
            Artisan::call('vendor:publish', [
                '--provider' => 'Wahyudedik\\LaravelAlert\\AlertServiceProvider',
                '--tag' => 'config',
                '--force' => $this->option('force')
            ]);

            $this->line('   ✓ Configuration published');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to publish configuration: ' . $e->getMessage());
        }
    }

    protected function publishAssets(): void
    {
        $this->info('🎨 Publishing assets...');

        try {
            Artisan::call('vendor:publish', [
                '--provider' => 'Wahyudedik\\LaravelAlert\\AlertServiceProvider',
                '--tag' => 'assets',
                '--force' => $this->option('force')
            ]);

            $this->line('   ✓ Assets published');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to publish assets: ' . $e->getMessage());
        }
    }

    protected function publishViews(): void
    {
        $this->info('👁️ Publishing views...');

        try {
            Artisan::call('vendor:publish', [
                '--provider' => 'Wahyudedik\\LaravelAlert\\AlertServiceProvider',
                '--tag' => 'views',
                '--force' => $this->option('force')
            ]);

            $this->line('   ✓ Views published');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to publish views: ' . $e->getMessage());
        }
    }

    protected function publishMigrations(): void
    {
        $this->info('🗄️ Publishing migrations...');

        try {
            Artisan::call('vendor:publish', [
                '--provider' => 'Wahyudedik\\LaravelAlert\\AlertServiceProvider',
                '--tag' => 'migrations',
                '--force' => $this->option('force')
            ]);

            $this->line('   ✓ Migrations published');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to publish migrations: ' . $e->getMessage());
        }
    }

    protected function publishTranslations(): void
    {
        $this->info('🌍 Publishing translations...');

        try {
            Artisan::call('vendor:publish', [
                '--provider' => 'Wahyudedik\\LaravelAlert\\AlertServiceProvider',
                '--tag' => 'translations',
                '--force' => $this->option('force')
            ]);

            $this->line('   ✓ Translations published');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to publish translations: ' . $e->getMessage());
        }
    }

    protected function runMigrations(): void
    {
        $this->info('🏃 Running migrations...');

        try {
            Artisan::call('migrate', ['--force' => true]);
            $this->line('   ✓ Migrations completed');
        } catch (\Exception $e) {
            $this->warn('   ⚠ Migration failed: ' . $e->getMessage());
            $this->line('   You can run migrations manually with: php artisan migrate');
        }
    }

    protected function createDirectories(): void
    {
        $this->info('📁 Creating directories...');

        $directories = [
            public_path('css/laravel-alert'),
            public_path('js/laravel-alert'),
            storage_path('app/laravel-alert'),
            storage_path('logs/laravel-alert'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->line("   ✓ Created: {$directory}");
            } else {
                $this->line("   ✓ Exists: {$directory}");
            }
        }
    }

    protected function updateComposerJson(): void
    {
        $this->info('📦 Updating composer.json...');

        try {
            $composerPath = base_path('composer.json');
            $composer = json_decode(File::get($composerPath), true);

            // Add scripts if not exists
            if (!isset($composer['scripts'])) {
                $composer['scripts'] = [];
            }

            if (!isset($composer['scripts']['laravel-alert'])) {
                $composer['scripts']['laravel-alert'] = [
                    'php artisan laravel-alert:install',
                    'php artisan laravel-alert:publish'
                ];
            }

            File::put($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->line('   ✓ Composer.json updated');
        } catch (\Exception $e) {
            $this->warn('   ⚠ Failed to update composer.json: ' . $e->getMessage());
        }
    }

    protected function displaySuccessMessage(): void
    {
        $this->newLine();
        $this->info('🎉 Laravel Alert installed successfully!');
        $this->newLine();

        $this->line('📚 <comment>Next steps:</comment>');
        $this->line('   1. Configure your settings in <comment>config/laravel-alert.php</comment>');
        $this->line('   2. Customize views in <comment>resources/views/vendor/laravel-alert</comment>');
        $this->line('   3. Add CSS/JS to your layout:');
        $this->line('      <comment>&lt;link rel="stylesheet" href="{{ asset(\'css/laravel-alert/laravel-alert.css\') }}"&gt;</comment>');
        $this->line('      <comment>&lt;script src="{{ asset(\'js/laravel-alert/laravel-alert.js\') }}"&gt;&lt;/script&gt;</comment>');
        $this->newLine();

        $this->line('🚀 <comment>Quick start:</comment>');
        $this->line('   <comment>use Wahyudedik\\LaravelAlert\\Facades\\Alert;</comment>');
        $this->line('   <comment>Alert::success(\'Welcome to Laravel Alert!\');</comment>');
        $this->newLine();

        $this->line('📖 <comment>Documentation:</comment>');
        $this->line('   <comment>https://wahyudedik.github.io/LaravelAlert</comment>');
        $this->newLine();

        $this->line('🤝 <comment>Support:</comment>');
        $this->line('   <comment>https://github.com/wahyudedik/LaravelAlert/issues</comment>');
    }
}
