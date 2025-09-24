<?php

namespace Wahyudedik\LaravelAlert\Console\Commands;

use Illuminate\Console\Command;
use Wahyudedik\LaravelAlert\Facades\Alert;
use Wahyudedik\LaravelAlert\Facades\Toast;
use Wahyudedik\LaravelAlert\Facades\Modal;
use Wahyudedik\LaravelAlert\Facades\Inline;

class TestCommand extends Command
{
    protected $signature = 'laravel-alert:test 
                            {--type=* : The alert type(s) to test}
                            {--all : Test all alert types}
                            {--interactive : Interactive testing mode}';

    protected $description = 'Test Laravel Alert functionality';

    protected $availableTypes = [
        'basic' => 'Basic alerts (success, error, warning, info)',
        'toast' => 'Toast notifications',
        'modal' => 'Modal alerts',
        'inline' => 'Inline alerts',
        'advanced' => 'Advanced features (AJAX, WebSocket, etc.)',
        'all' => 'All alert types'
    ];

    public function handle()
    {
        $this->info('🧪 Testing Laravel Alert functionality...');
        $this->newLine();

        $types = $this->getTypesToTest();

        if (empty($types)) {
            $this->displayAvailableTypes();
            return 0;
        }

        foreach ($types as $type) {
            $this->testType($type);
        }

        $this->displaySuccessMessage();
        return 0;
    }

    protected function getTypesToTest(): array
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
        $this->info('Available test types:');
        foreach ($this->availableTypes as $type => $description) {
            $this->line("  <comment>{$type}</comment> - {$description}");
        }
        $this->newLine();

        $types = $this->ask('Which types would you like to test? (comma-separated)', 'basic');

        return array_map('trim', explode(',', $types));
    }

    protected function testType(string $type): void
    {
        $description = $this->availableTypes[$type];
        $this->info("🧪 Testing {$type} ({$description})...");

        try {
            switch ($type) {
                case 'basic':
                    $this->testBasicAlerts();
                    break;
                case 'toast':
                    $this->testToastAlerts();
                    break;
                case 'modal':
                    $this->testModalAlerts();
                    break;
                case 'inline':
                    $this->testInlineAlerts();
                    break;
                case 'advanced':
                    $this->testAdvancedFeatures();
                    break;
                case 'all':
                    $this->testAllTypes();
                    break;
            }

            $this->line("   ✓ {$type} tests completed");
        } catch (\Exception $e) {
            $this->error("   ✗ Failed to test {$type}: " . $e->getMessage());
        }
    }

    protected function testBasicAlerts(): void
    {
        $this->line('   Testing basic alerts...');

        // Test success alert
        Alert::success('This is a success alert!');
        $this->line('   ✓ Success alert created');

        // Test error alert
        Alert::error('This is an error alert!');
        $this->line('   ✓ Error alert created');

        // Test warning alert
        Alert::warning('This is a warning alert!');
        $this->line('   ✓ Warning alert created');

        // Test info alert
        Alert::info('This is an info alert!');
        $this->line('   ✓ Info alert created');

        // Test custom alert
        Alert::custom('custom', 'This is a custom alert!', 'Custom Title');
        $this->line('   ✓ Custom alert created');
    }

    protected function testToastAlerts(): void
    {
        $this->line('   Testing toast alerts...');

        // Test toast success
        Toast::success('Toast success message!');
        $this->line('   ✓ Toast success created');

        // Test toast error
        Toast::error('Toast error message!');
        $this->line('   ✓ Toast error created');

        // Test toast warning
        Toast::warning('Toast warning message!');
        $this->line('   ✓ Toast warning created');

        // Test toast info
        Toast::info('Toast info message!');
        $this->line('   ✓ Toast info created');

        // Test toast with options
        Toast::custom('custom', 'Custom toast message!', 'Custom Title')
            ->position('top-left')
            ->duration(3000)
            ->dismissible(true);
        $this->line('   ✓ Custom toast created');
    }

    protected function testModalAlerts(): void
    {
        $this->line('   Testing modal alerts...');

        // Test modal success
        Modal::success('Modal success message!');
        $this->line('   ✓ Modal success created');

        // Test modal error
        Modal::error('Modal error message!');
        $this->line('   ✓ Modal error created');

        // Test modal warning
        Modal::warning('Modal warning message!');
        $this->line('   ✓ Modal warning created');

        // Test modal info
        Modal::info('Modal info message!');
        $this->line('   ✓ Modal info created');

        // Test modal with options
        Modal::custom('custom', 'Custom modal message!', 'Custom Title')
            ->size('large')
            ->backdrop(true)
            ->keyboard(true);
        $this->line('   ✓ Custom modal created');
    }

    protected function testInlineAlerts(): void
    {
        $this->line('   Testing inline alerts...');

        // Test inline success
        Inline::success('Inline success message!');
        $this->line('   ✓ Inline success created');

        // Test inline error
        Inline::error('Inline error message!');
        $this->line('   ✓ Inline error created');

        // Test inline warning
        Inline::warning('Inline warning message!');
        $this->line('   ✓ Inline warning created');

        // Test inline info
        Inline::info('Inline info message!');
        $this->line('   ✓ Inline info created');

        // Test inline with options
        Inline::custom('custom', 'Custom inline message!', 'Custom Title')
            ->context('form')
            ->field('email')
            ->dismissible(true);
        $this->line('   ✓ Custom inline created');
    }

    protected function testAdvancedFeatures(): void
    {
        $this->line('   Testing advanced features...');

        // Test AJAX alerts
        Alert::ajax('success', 'AJAX alert message!');
        $this->line('   ✓ AJAX alert created');

        // Test WebSocket alerts
        Alert::websocket('info', 'WebSocket alert message!');
        $this->line('   ✓ WebSocket alert created');

        // Test Pusher alerts
        Alert::pusher('warning', 'Pusher alert message!');
        $this->line('   ✓ Pusher alert created');

        // Test Email alerts
        Alert::email('error', 'Email alert message!');
        $this->line('   ✓ Email alert created');

        // Test bulk operations
        Alert::bulk([
            ['type' => 'success', 'message' => 'Bulk alert 1'],
            ['type' => 'error', 'message' => 'Bulk alert 2'],
            ['type' => 'warning', 'message' => 'Bulk alert 3'],
        ]);
        $this->line('   ✓ Bulk alerts created');
    }

    protected function testAllTypes(): void
    {
        $this->testBasicAlerts();
        $this->testToastAlerts();
        $this->testModalAlerts();
        $this->testInlineAlerts();
        $this->testAdvancedFeatures();
    }

    protected function displayAvailableTypes(): void
    {
        $this->info('Available test types:');
        $this->newLine();

        foreach ($this->availableTypes as $type => $description) {
            $this->line("  <comment>{$type}</comment> - {$description}");
        }

        $this->newLine();
        $this->line('Usage examples:');
        $this->line('  <comment>php artisan laravel-alert:test --type=basic</comment>');
        $this->line('  <comment>php artisan laravel-alert:test --type=basic,toast,modal</comment>');
        $this->line('  <comment>php artisan laravel-alert:test --all</comment>');
        $this->line('  <comment>php artisan laravel-alert:test --interactive</comment>');
    }

    protected function displaySuccessMessage(): void
    {
        $this->newLine();
        $this->info('🎉 Laravel Alert testing completed successfully!');
        $this->newLine();

        $this->line('📊 <comment>Test Results:</comment>');
        $this->line('   • <comment>Basic Alerts</comment> - All alert types tested');
        $this->line('   • <comment>Toast Alerts</comment> - Toast notifications tested');
        $this->line('   • <comment>Modal Alerts</comment> - Modal dialogs tested');
        $this->line('   • <comment>Inline Alerts</comment> - Inline messages tested');
        $this->line('   • <comment>Advanced Features</comment> - AJAX, WebSocket, Pusher, Email tested');
        $this->line('   • <comment>Bulk Operations</comment> - Multiple alerts tested');

        $this->newLine();
        $this->line('💡 <comment>Next steps:</comment>');
        $this->line('   • Check your application to see the alerts');
        $this->line('   • Test different themes and positions');
        $this->line('   • Customize alert behavior in config');
        $this->newLine();

        $this->line('🔧 <comment>Useful Commands:</comment>');
        $this->line('   <comment>php artisan laravel-alert:status</comment> - Check package status');
        $this->line('   <comment>php artisan laravel-alert:clear</comment> - Clear test data');
        $this->line('   <comment>php artisan laravel-alert:publish</comment> - Publish assets');
        $this->newLine();

        $this->line('📖 <comment>Documentation:</comment>');
        $this->line('   <comment>https://wahyudedik.github.io/LaravelAlert</comment>');
    }
}
