<?php

namespace Wahyudedik\LaravelAlert\View\Components;

use Illuminate\View\Component;
use Wahyudedik\LaravelAlert\Managers\AlertManager;

class AlertsComponent extends Component
{
    public array $alerts;
    public array $config;

    /**
     * Create a new component instance.
     */
    public function __construct(AlertManager $alertManager)
    {
        $this->alerts = $alertManager->getAlerts();
        $this->config = config('laravel-alert', []);

        // Clear alerts after retrieving them
        $alertManager->clear();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        $theme = $this->config['default_theme'] ?? 'bootstrap';
        return "laravel-alert::components.{$theme}.alerts";
    }
}
