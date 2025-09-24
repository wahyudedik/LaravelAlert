<?php

namespace Wahyudedik\LaravelAlert\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PublishCommand extends Command
{
    protected $signature = 'laravel-alert:publish 
                            {--tag=* : The tag(s) to publish}
                            {--force : Overwrite existing files}
                            {--all : Publish all assets}';

    protected $description = 'Publish Laravel Alert assets for customization';

    protected $availableTags = [
        'config' => 'Configuration files',
        'views' => 'Blade view templates',
        'assets' => 'CSS and JavaScript assets',
        'migrations' => 'Database migrations',
        'translations' => 'Language files',
        'routes' => 'Route definitions',
        'middleware' => 'Middleware classes',
        'commands' => 'Console commands',
        'all' => 'All assets'
    ];

    public function handle()
    {
        $this->info('📦 Publishing Laravel Alert assets...');
        $this->newLine();

        $tags = $this->getTagsToPublish();

        if (empty($tags)) {
            $this->displayAvailableTags();
            return 0;
        }

        foreach ($tags as $tag) {
            $this->publishTag($tag);
        }

        $this->displaySuccessMessage();
        return 0;
    }

    protected function getTagsToPublish(): array
    {
        $tags = $this->option('tag');

        if ($this->option('all')) {
            return array_keys($this->availableTags);
        }

        if (empty($tags)) {
            $tags = $this->askForTags();
        }

        return array_filter($tags, function ($tag) {
            return array_key_exists($tag, $this->availableTags);
        });
    }

    protected function askForTags(): array
    {
        $this->info('Available tags:');
        foreach ($this->availableTags as $tag => $description) {
            $this->line("  <comment>{$tag}</comment> - {$description}");
        }
        $this->newLine();

        $tags = $this->ask('Which tags would you like to publish? (comma-separated)', 'config,views,assets');

        return array_map('trim', explode(',', $tags));
    }

    protected function publishTag(string $tag): void
    {
        $description = $this->availableTags[$tag];
        $this->info("📤 Publishing {$tag} ({$description})...");

        try {
            Artisan::call('vendor:publish', [
                '--provider' => 'Wahyudedik\\LaravelAlert\\AlertServiceProvider',
                '--tag' => $tag,
                '--force' => $this->option('force')
            ]);

            $this->line("   ✓ {$tag} published successfully");
        } catch (\Exception $e) {
            $this->error("   ✗ Failed to publish {$tag}: " . $e->getMessage());
        }
    }

    protected function displayAvailableTags(): void
    {
        $this->info('Available tags:');
        $this->newLine();

        foreach ($this->availableTags as $tag => $description) {
            $this->line("  <comment>{$tag}</comment> - {$description}");
        }

        $this->newLine();
        $this->line('Usage examples:');
        $this->line('  <comment>php artisan laravel-alert:publish --tag=config</comment>');
        $this->line('  <comment>php artisan laravel-alert:publish --tag=config,views,assets</comment>');
        $this->line('  <comment>php artisan laravel-alert:publish --all</comment>');
    }

    protected function displaySuccessMessage(): void
    {
        $this->newLine();
        $this->info('🎉 Assets published successfully!');
        $this->newLine();

        $this->line('📁 <comment>Published files:</comment>');

        if (in_array('config', $this->getTagsToPublish())) {
            $this->line('   • <comment>config/laravel-alert.php</comment> - Main configuration');
        }

        if (in_array('views', $this->getTagsToPublish())) {
            $this->line('   • <comment>resources/views/vendor/laravel-alert/</comment> - Blade templates');
        }

        if (in_array('assets', $this->getTagsToPublish())) {
            $this->line('   • <comment>public/css/laravel-alert/</comment> - CSS files');
            $this->line('   • <comment>public/js/laravel-alert/</comment> - JavaScript files');
        }

        if (in_array('migrations', $this->getTagsToPublish())) {
            $this->line('   • <comment>database/migrations/</comment> - Database migrations');
        }

        if (in_array('translations', $this->getTagsToPublish())) {
            $this->line('   • <comment>resources/lang/</comment> - Language files');
        }

        if (in_array('routes', $this->getTagsToPublish())) {
            $this->line('   • <comment>routes/</comment> - Route definitions');
        }

        if (in_array('middleware', $this->getTagsToPublish())) {
            $this->line('   • <comment>app/Http/Middleware/</comment> - Middleware classes');
        }

        if (in_array('commands', $this->getTagsToPublish())) {
            $this->line('   • <comment>app/Console/Commands/</comment> - Console commands');
        }

        $this->newLine();
        $this->line('🔧 <comment>Customization tips:</comment>');
        $this->line('   • Edit <comment>config/laravel-alert.php</comment> to change default settings');
        $this->line('   • Modify views in <comment>resources/views/vendor/laravel-alert/</comment>');
        $this->line('   • Customize CSS in <comment>public/css/laravel-alert/</comment>');
        $this->line('   • Add custom JavaScript in <comment>public/js/laravel-alert/</comment>');
        $this->newLine();

        $this->line('📖 <comment>Documentation:</comment>');
        $this->line('   <comment>https://wahyudedik.github.io/LaravelAlert</comment>');
    }
}
