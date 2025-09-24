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
    public ?int $expiresAt;
    public ?int $autoDismissDelay;
    public ?string $animation;
    public ?string $position;
    public ?string $theme;
    public ?array $dataAttributes;
    public ?string $htmlContent;

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
        $this->expiresAt = $options['expires_at'] ?? null;
        $this->autoDismissDelay = $options['auto_dismiss_delay'] ?? null;
        $this->animation = $options['animation'] ?? null;
        $this->position = $options['position'] ?? null;
        $this->theme = $options['theme'] ?? null;
        $this->dataAttributes = $options['data_attributes'] ?? null;
        $this->htmlContent = $options['html_content'] ?? null;
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
     * Get expiration timestamp.
     */
    public function getExpiresAt(): ?int
    {
        return $this->expiresAt;
    }

    /**
     * Check if the alert has expired.
     */
    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return time() > $this->expiresAt;
    }

    /**
     * Get auto-dismiss delay in milliseconds.
     */
    public function getAutoDismissDelay(): ?int
    {
        return $this->autoDismissDelay;
    }

    /**
     * Check if the alert should auto-dismiss.
     */
    public function shouldAutoDismiss(): bool
    {
        return $this->autoDismissDelay !== null && $this->autoDismissDelay > 0;
    }

    /**
     * Get animation type.
     */
    public function getAnimation(): ?string
    {
        return $this->animation;
    }

    /**
     * Get position.
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * Get theme.
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * Get data attributes.
     */
    public function getDataAttributes(): ?array
    {
        return $this->dataAttributes;
    }

    /**
     * Get HTML content.
     */
    public function getHtmlContent(): ?string
    {
        return $this->htmlContent;
    }

    /**
     * Set expiration time.
     */
    public function setExpiresAt(?int $timestamp): self
    {
        $this->expiresAt = $timestamp;
        return $this;
    }

    /**
     * Set auto-dismiss delay.
     */
    public function setAutoDismissDelay(?int $delay): self
    {
        $this->autoDismissDelay = $delay;
        return $this;
    }

    /**
     * Set animation.
     */
    public function setAnimation(?string $animation): self
    {
        $this->animation = $animation;
        return $this;
    }

    /**
     * Set position.
     */
    public function setPosition(?string $position): self
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Set theme.
     */
    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * Add data attribute.
     */
    public function addDataAttribute(string $key, string $value): self
    {
        if ($this->dataAttributes === null) {
            $this->dataAttributes = [];
        }

        $this->dataAttributes[$key] = $value;
        return $this;
    }

    /**
     * Set HTML content.
     */
    public function setHtmlContent(?string $content): self
    {
        $this->htmlContent = $content;
        return $this;
    }

    /**
     * Get all CSS classes including theme classes.
     */
    public function getAllClasses(): string
    {
        $classes = [];

        if ($this->class) {
            $classes[] = $this->class;
        }

        if ($this->theme) {
            $classes[] = "alert-{$this->theme}";
        }

        if ($this->animation) {
            $classes[] = "alert-{$this->animation}";
        }

        return implode(' ', $classes);
    }

    /**
     * Get all data attributes as HTML string.
     */
    public function getDataAttributesHtml(): string
    {
        if (!$this->dataAttributes) {
            return '';
        }

        $html = '';
        foreach ($this->dataAttributes as $key => $value) {
            $html .= " data-{$key}=\"" . htmlspecialchars($value, ENT_QUOTES) . "\"";
        }

        return $html;
    }

    /**
     * Check if alert is valid (not expired).
     */
    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    /**
     * Get time until expiration in seconds.
     */
    public function getTimeUntilExpiration(): ?int
    {
        if ($this->expiresAt === null) {
            return null;
        }

        $timeLeft = $this->expiresAt - time();
        return $timeLeft > 0 ? $timeLeft : 0;
    }

    /**
     * Convert the alert to an array with all properties.
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
            'expires_at' => $this->expiresAt,
            'auto_dismiss_delay' => $this->autoDismissDelay,
            'animation' => $this->animation,
            'position' => $this->position,
            'theme' => $this->theme,
            'data_attributes' => $this->dataAttributes,
            'html_content' => $this->htmlContent,
            'options' => $this->options,
            'is_expired' => $this->isExpired(),
            'should_auto_dismiss' => $this->shouldAutoDismiss(),
            'is_valid' => $this->isValid(),
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
