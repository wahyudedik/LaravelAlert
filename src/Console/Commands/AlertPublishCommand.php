<?php

namespace Wahyudedik\LaravelAlert\Console\Commands;

use Illuminate\Console\Command;

class AlertPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'alert:publish 
                            {--theme= : Specify which theme to publish (bootstrap, tailwind, bulma)}
                            {--force : Force the publication even if files already exist}';

    /**
     * The console command description.
     */
    protected $description = 'Publish Laravel Alert views and assets for customization';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $theme = $this->option('theme');
        $force = $this->option('force');

        if ($theme) {
            $this->publishSpecificTheme($theme, $force);
        } else {
            $this->publishAllResources($force);
        }

        $this->info('Laravel Alert resources published successfully!');
        $this->line('');
        $this->line('You can now customize the views in:');
        $this->line('- resources/views/vendor/laravel-alert/');
        $this->line('- public/vendor/laravel-alert/');

        return 0;
    }

    /**
     * Publish all resources.
     */
    protected function publishAllResources(bool $force): void
    {
        $this->info('Publishing all Laravel Alert resources...');

        // Publish configuration
        $this->call('vendor:publish', [
            '--provider' => 'Wahyudedik\LaravelAlert\AlertServiceProvider',
            '--tag' => 'laravel-alert-config',
            '--force' => $force
        ]);

        // Publish views
        $this->call('vendor:publish', [
            '--provider' => 'Wahyudedik\LaravelAlert\AlertServiceProvider',
            '--tag' => 'laravel-alert-views',
            '--force' => $force
        ]);

        // Publish assets
        $this->call('vendor:publish', [
            '--provider' => 'Wahyudedik\LaravelAlert\AlertServiceProvider',
            '--tag' => 'laravel-alert-assets',
            '--force' => $force
        ]);
    }

    /**
     * Publish specific theme.
     */
    protected function publishSpecificTheme(string $theme, bool $force): void
    {
        $this->info("Publishing Laravel Alert resources for theme: {$theme}");

        // Validate theme
        $validThemes = ['bootstrap', 'tailwind', 'bulma'];
        if (!in_array($theme, $validThemes)) {
            $this->error("Invalid theme. Valid themes are: " . implode(', ', $validThemes));
            return;
        }

        // Publish configuration
        $this->call('vendor:publish', [
            '--provider' => 'Wahyudedik\LaravelAlert\AlertServiceProvider',
            '--tag' => 'laravel-alert-config',
            '--force' => $force
        ]);

        // Publish views
        $this->call('vendor:publish', [
            '--provider' => 'Wahyudedik\LaravelAlert\AlertServiceProvider',
            '--tag' => 'laravel-alert-views',
            '--force' => $force
        ]);

        $this->line("Theme '{$theme}' resources published successfully!");
        $this->line("You can customize the {$theme} theme views in:");
        $this->line("- resources/views/vendor/laravel-alert/components/{$theme}/");
    }
}
