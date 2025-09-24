<?php

namespace Wahyudedik\LaravelAlert\Models;

class Alert
{
    public string $type;
    public string $message;
    public ?string $title;
    public array $options;
    public string $id;
    public bool $dismissible;
    public ?string $icon;
    public ?string $class;
    public ?string $style;

    public function __construct(
        string $type,
        string $message,
        ?string $title = null,
        array $options = []
    ) {
        $this->type = $type;
        $this->message = $message;
        $this->title = $title;
        $this->options = $options;
        $this->id = $this->generateId();
        $this->dismissible = $options['dismissible'] ?? true;
        $this->icon = $options['icon'] ?? null;
        $this->class = $options['class'] ?? null;
        $this->style = $options['style'] ?? null;
    }

    /**
     * Get the alert type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the alert message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the alert title.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Get the alert ID.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Check if the alert is dismissible.
     */
    public function isDismissible(): bool
    {
        return $this->dismissible;
    }

    /**
     * Get the alert icon.
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Get custom CSS class.
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * Get custom CSS style.
     */
    public function getStyle(): ?string
    {
        return $this->style;
    }

    /**
     * Get all options.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get a specific option.
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Convert the alert to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'message' => $this->message,
            'title' => $this->title,
            'dismissible' => $this->dismissible,
            'icon' => $this->icon,
            'class' => $this->class,
            'style' => $this->style,
            'options' => $this->options,
        ];
    }

    /**
     * Generate a unique ID for the alert.
     */
    protected function generateId(): string
    {
        return 'alert_' . uniqid() . '_' . mt_rand(1000, 9999);
    }
}
