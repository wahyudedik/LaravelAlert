<?php

namespace Wahyudedik\LaravelAlert\View\Components;

use Illuminate\View\Component;
use Wahyudedik\LaravelAlert\Models\Alert;

class AlertComponent extends Component
{
    public Alert $alert;
    public array $config;
    public ?string $theme;
    public ?string $animation;
    public ?string $position;
    public bool $dismissible;
    public ?string $icon;
    public ?string $class;
    public ?string $style;
    public ?array $dataAttributes;
    public ?string $htmlContent;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $type,
        string $message,
        ?string $title = null,
        array $options = [],
        ?string $theme = null,
        ?string $animation = null,
        ?string $position = null,
        bool $dismissible = true,
        ?string $icon = null,
        ?string $class = null,
        ?string $style = null,
        ?array $dataAttributes = null,
        ?string $htmlContent = null
    ) {
        // Merge options with component attributes
        $mergedOptions = array_merge($options, [
            'theme' => $theme,
            'animation' => $animation,
            'position' => $position,
            'dismissible' => $dismissible,
            'icon' => $icon,
            'class' => $class,
            'style' => $style,
            'data_attributes' => $dataAttributes,
            'html_content' => $htmlContent,
        ]);

        $this->alert = new Alert($type, $message, $title, $mergedOptions);
        $this->config = config('laravel-alert', []);

        // Set component properties for easy access in templates
        $this->theme = $theme ?? $this->config['default_theme'] ?? 'bootstrap';
        $this->animation = $animation ?? $this->config['animation'] ?? 'fade';
        $this->position = $position ?? $this->config['position'] ?? 'top-right';
        $this->dismissible = $dismissible;
        $this->icon = $icon;
        $this->class = $class;
        $this->style = $style;
        $this->dataAttributes = $dataAttributes;
        $this->htmlContent = $htmlContent;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        $theme = $this->theme ?? $this->config['default_theme'] ?? 'bootstrap';
        return "laravel-alert::components.{$theme}.alert";
    }

    /**
     * Get all CSS classes for the alert.
     */
    public function getAllClasses(): string
    {
        return $this->alert->getAllClasses();
    }

    /**
     * Get data attributes as HTML string.
     */
    public function getDataAttributesHtml(): string
    {
        return $this->alert->getDataAttributesHtml();
    }

    /**
     * Check if alert should auto-dismiss.
     */
    public function shouldAutoDismiss(): bool
    {
        return $this->alert->shouldAutoDismiss();
    }

    /**
     * Check if alert is expired.
     */
    public function isExpired(): bool
    {
        return $this->alert->isExpired();
    }

    /**
     * Check if alert is valid.
     */
    public function isValid(): bool
    {
        return $this->alert->isValid();
    }
}
