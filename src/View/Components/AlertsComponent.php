<?php

namespace Wahyudedik\LaravelAlert\View\Components;

use Illuminate\View\Component;
use Wahyudedik\LaravelAlert\Managers\AlertManager;

class AlertsComponent extends Component
{
    public array $alerts;
    public array $config;
    public ?string $theme;
    public ?string $position;
    public ?string $animation;
    public bool $autoClear;
    public ?int $maxAlerts;
    public ?string $containerClass;
    public ?string $containerStyle;

    /**
     * Create a new component instance.
     */
    public function __construct(
        AlertManager $alertManager,
        ?string $theme = null,
        ?string $position = null,
        ?string $animation = null,
        bool $autoClear = true,
        ?int $maxAlerts = null,
        ?string $containerClass = null,
        ?string $containerStyle = null
    ) {
        $this->alerts = $alertManager->getAlerts();
        $this->config = config('laravel-alert', []);

        // Set component properties
        $this->theme = $theme ?? $this->config['default_theme'] ?? 'bootstrap';
        $this->position = $position ?? $this->config['position'] ?? 'top-right';
        $this->animation = $animation ?? $this->config['animation'] ?? 'fade';
        $this->autoClear = $autoClear;
        $this->maxAlerts = $maxAlerts ?? $this->config['max_alerts'] ?? 5;
        $this->containerClass = $containerClass;
        $this->containerStyle = $containerStyle;

        // Clear alerts after retrieving them if auto-clear is enabled
        if ($this->autoClear) {
            $alertManager->clear();
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        $theme = $this->theme ?? $this->config['default_theme'] ?? 'bootstrap';
        return "laravel-alert::components.{$theme}.alerts";
    }

    /**
     * Get alerts count.
     */
    public function getAlertsCount(): int
    {
        return count($this->alerts);
    }

    /**
     * Check if there are any alerts.
     */
    public function hasAlerts(): bool
    {
        return $this->getAlertsCount() > 0;
    }

    /**
     * Get alerts by type.
     */
    public function getAlertsByType(string $type): array
    {
        return array_filter($this->alerts, function ($alert) use ($type) {
            return $alert->getType() === $type;
        });
    }

    /**
     * Get container classes.
     */
    public function getContainerClasses(): string
    {
        $classes = ['laravel-alerts-container'];

        if ($this->containerClass) {
            $classes[] = $this->containerClass;
        }

        if ($this->animation) {
            $classes[] = "alert-{$this->animation}";
        }

        return implode(' ', $classes);
    }

    /**
     * Get container styles.
     */
    public function getContainerStyles(): string
    {
        $styles = [];

        if ($this->containerStyle) {
            $styles[] = $this->containerStyle;
        }

        // Add position styles
        $positionStyles = $this->getPositionStyles();
        if ($positionStyles) {
            $styles[] = $positionStyles;
        }

        return implode('; ', $styles);
    }

    /**
     * Get position styles based on position setting.
     */
    protected function getPositionStyles(): string
    {
        switch ($this->position) {
            case 'top-right':
                return 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
            case 'top-left':
                return 'position: fixed; top: 20px; left: 20px; z-index: 9999;';
            case 'bottom-right':
                return 'position: fixed; bottom: 20px; right: 20px; z-index: 9999;';
            case 'bottom-left':
                return 'position: fixed; bottom: 20px; left: 20px; z-index: 9999;';
            case 'top-center':
                return 'position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;';
            case 'bottom-center':
                return 'position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;';
            default:
                return 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
        }
    }

    /**
     * Get data attributes for the container.
     */
    public function getContainerDataAttributes(): string
    {
        $attributes = [
            'data-position' => $this->position,
            'data-max-alerts' => $this->maxAlerts,
            'data-theme' => $this->theme,
            'data-animation' => $this->animation,
        ];

        $html = '';
        foreach ($attributes as $key => $value) {
            $html .= " {$key}=\"" . htmlspecialchars($value, ENT_QUOTES) . "\"";
        }

        return $html;
    }
}
