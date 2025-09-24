<?php

namespace Wahyudedik\LaravelAlert\Console\Commands;

use Illuminate\Console\Command;
use Wahyudedik\LaravelAlert\Facades\Alert;

class AlertClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'alert:clear 
                            {--type= : Clear alerts of specific type (success, error, warning, info)}
                            {--expired : Clear only expired alerts}
                            {--all : Clear all alerts including non-expired}';

    /**
     * The console command description.
     */
    protected $description = 'Clear Laravel Alert notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->option('type');
        $expired = $this->option('expired');
        $all = $this->option('all');

        if ($type) {
            $this->clearByType($type);
        } elseif ($expired) {
            $this->clearExpired();
        } elseif ($all) {
            $this->clearAll();
        } else {
            $this->clearAll();
        }

        return 0;
    }

    /**
     * Clear alerts by type.
     */
    protected function clearByType(string $type): void
    {
        $validTypes = ['success', 'error', 'warning', 'info'];

        if (!in_array($type, $validTypes)) {
            $this->error("Invalid type. Valid types are: " . implode(', ', $validTypes));
            return;
        }

        $alerts = Alert::getAlertsByType($type);
        $count = count($alerts);

        Alert::clearByType($type);

        $this->info("Cleared {$count} {$type} alerts.");
    }

    /**
     * Clear expired alerts.
     */
    protected function clearExpired(): void
    {
        $expiredAlerts = Alert::getExpiredAlerts();
        $count = count($expiredAlerts);

        Alert::cleanupExpired();

        $this->info("Cleared {$count} expired alerts.");
    }

    /**
     * Clear all alerts.
     */
    protected function clearAll(): void
    {
        $alerts = Alert::getAlerts();
        $count = count($alerts);

        Alert::clear();

        $this->info("Cleared {$count} alerts.");
    }
}
