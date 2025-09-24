<?php

namespace Wahyudedik\LaravelAlert\View\Components;

use Illuminate\View\Component;
use Wahyudedik\LaravelAlert\Models\Alert;

class AlertComponent extends Component
{
    public Alert $alert;
    public array $config;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $type,
        string $message,
        ?string $title = null,
        array $options = []
    ) {
        $this->alert = new Alert($type, $message, $title, $options);
        $this->config = config('laravel-alert', []);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        $theme = $this->config['default_theme'] ?? 'bootstrap';
        return "laravel-alert::components.{$theme}.alert";
    }
}
