<?php

namespace Wahyudedik\LaravelAlert\Managers;

use Illuminate\Session\Store;
use Wahyudedik\LaravelAlert\Models\Alert;
use Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface;

class InlineAlertManager implements AlertManagerInterface
{
    protected Store $session;
    protected array $config;
    protected string $sessionKey;

    public function __construct(Store $session)
    {
        $this->session = $session;
        $this->config = config('laravel-alert', []);
        $this->sessionKey = $this->config['session_key'] ?? 'laravel_alerts';
    }

    /**
     * Add a success inline alert.
     */
    public function success(string $message, ?string $title = null, array $options = []): self
    {
        return $this->addInline('success', $message, $title, $options);
    }

    /**
     * Add an error inline alert.
     */
    public function error(string $message, ?string $title = null, array $options = []): self
    {
        return $this->addInline('error', $message, $title, $options);
    }

    /**
     * Add a warning inline alert.
     */
    public function warning(string $message, ?string $title = null, array $options = []): self
    {
        return $this->addInline('warning', $message, $title, $options);
    }

    /**
     * Add an info inline alert.
     */
    public function info(string $message, ?string $title = null, array $options = []): self
    {
        return $this->addInline('info', $message, $title, $options);
    }

    /**
     * Add an inline alert with custom type.
     */
    public function add(string $type, string $message, ?string $title = null, array $options = []): self
    {
        return $this->addInline($type, $message, $title, $options);
    }

    /**
     * Add an inline alert.
     */
    public function addInline(string $type, string $message, ?string $title = null, array $options = []): self
    {
        $defaultOptions = [
            'inline' => true,
            'position' => $options['position'] ?? 'relative',
            'context' => $options['context'] ?? 'general',
            'field' => $options['field'] ?? null,
            'form' => $options['form'] ?? null,
            'dismissible' => $options['dismissible'] ?? true,
            'animation' => $options['animation'] ?? 'fade',
            'sticky' => $options['sticky'] ?? false,
            'auto_clear' => $options['auto_clear'] ?? true,
        ];

        $options = array_merge($defaultOptions, $options);

        $alert = new Alert($type, $message, $title, $options);

        $inlineAlerts = $this->getInlineAlerts();
        $inlineAlerts[] = $alert;

        // Limit the number of inline alerts
        $maxInlineAlerts = $options['max_inline_alerts'] ?? $this->config['max_inline_alerts'] ?? 10;
        if (count($inlineAlerts) > $maxInlineAlerts) {
            $inlineAlerts = array_slice($inlineAlerts, -$maxInlineAlerts);
        }

        $this->session->put($this->getInlineSessionKey(), $inlineAlerts);

        return $this;
    }

    /**
     * Add a field-specific validation alert.
     */
    public function fieldError(string $field, string $message, array $options = []): self
    {
        $options['field'] = $field;
        $options['context'] = 'validation';
        $options['type'] = 'error';
        $options['dismissible'] = false; // Validation errors should not be dismissible

        return $this->addInline('error', $message, "Validation Error for {$field}", $options);
    }

    /**
     * Add a form-level alert.
     */
    public function formAlert(string $type, string $message, ?string $title = null, array $options = []): self
    {
        $options['context'] = 'form';
        $options['position'] = 'form-top';

        return $this->addInline($type, $message, $title, $options);
    }

    /**
     * Add a contextual alert.
     */
    public function contextual(string $type, string $message, string $context, array $options = []): self
    {
        $options['context'] = $context;
        $options['position'] = 'contextual';

        return $this->addInline($type, $message, null, $options);
    }

    /**
     * Get all inline alerts.
     */
    public function getAlerts(): array
    {
        return $this->getInlineAlerts();
    }

    /**
     * Get all inline alerts.
     */
    public function getInlineAlerts(): array
    {
        $alerts = $this->session->get($this->getInlineSessionKey(), []);

        // Filter out expired alerts
        $validAlerts = array_filter($alerts, function ($alert) {
            return $alert->isValid();
        });

        // Update session with valid alerts only
        if (count($validAlerts) !== count($alerts)) {
            $this->session->put($this->getInlineSessionKey(), array_values($validAlerts));
        }

        return $validAlerts;
    }

    /**
     * Get alerts by context.
     */
    public function getAlertsByContext(string $context): array
    {
        return array_filter($this->getInlineAlerts(), function ($alert) use ($context) {
            return $alert->getOption('context') === $context;
        });
    }

    /**
     * Get alerts by field.
     */
    public function getAlertsByField(string $field): array
    {
        return array_filter($this->getInlineAlerts(), function ($alert) use ($field) {
            return $alert->getOption('field') === $field;
        });
    }

    /**
     * Get validation alerts.
     */
    public function getValidationAlerts(): array
    {
        return $this->getAlertsByContext('validation');
    }

    /**
     * Get form alerts.
     */
    public function getFormAlerts(): array
    {
        return $this->getAlertsByContext('form');
    }

    /**
     * Clear all inline alerts.
     */
    public function clear(): self
    {
        $this->session->forget($this->getInlineSessionKey());
        return $this;
    }

    /**
     * Clear alerts by context.
     */
    public function clearByContext(string $context): self
    {
        $alerts = $this->getInlineAlerts();
        $filteredAlerts = array_filter($alerts, function ($alert) use ($context) {
            return $alert->getOption('context') !== $context;
        });

        $this->session->put($this->getInlineSessionKey(), array_values($filteredAlerts));
        return $this;
    }

    /**
     * Clear alerts by field.
     */
    public function clearByField(string $field): self
    {
        $alerts = $this->getInlineAlerts();
        $filteredAlerts = array_filter($alerts, function ($alert) use ($field) {
            return $alert->getOption('field') !== $field;
        });

        $this->session->put($this->getInlineSessionKey(), array_values($filteredAlerts));
        return $this;
    }

    /**
     * Render a single inline alert.
     */
    public function render(string $type, string $message, ?string $title = null, array $options = []): string
    {
        $alert = new Alert($type, $message, $title, $options);
        return $this->renderInlineAlert($alert);
    }

    /**
     * Render all inline alerts.
     */
    public function renderAll(): string
    {
        $alerts = $this->getInlineAlerts();
        $html = '';

        foreach ($alerts as $alert) {
            $html .= $this->renderInlineAlert($alert);
        }

        // Clear alerts after rendering if auto-clear is enabled
        if ($this->config['inline_auto_clear'] ?? true) {
            $this->clear();
        }

        return $html;
    }

    /**
     * Render inline alerts by context.
     */
    public function renderByContext(string $context): string
    {
        $alerts = $this->getAlertsByContext($context);
        $html = '';

        foreach ($alerts as $alert) {
            $html .= $this->renderInlineAlert($alert);
        }

        return $html;
    }

    /**
     * Render inline alerts by field.
     */
    public function renderByField(string $field): string
    {
        $alerts = $this->getAlertsByField($field);
        $html = '';

        foreach ($alerts as $alert) {
            $html .= $this->renderInlineAlert($alert);
        }

        return $html;
    }

    /**
     * Render a single inline alert object.
     */
    protected function renderInlineAlert(Alert $alert): string
    {
        $theme = $this->config['default_theme'] ?? 'bootstrap';
        $view = "laravel-alert::inline.{$theme}.alert";

        return view($view, [
            'alert' => $alert,
            'config' => $this->config,
        ])->render();
    }

    /**
     * Get the session key for inline alerts.
     */
    protected function getInlineSessionKey(): string
    {
        return $this->sessionKey . '_inline';
    }

    /**
     * Get inline alerts count.
     */
    public function count(): int
    {
        return count($this->getInlineAlerts());
    }

    /**
     * Check if there are any inline alerts.
     */
    public function hasAlerts(): bool
    {
        return $this->count() > 0;
    }

    /**
     * Get alerts by type.
     */
    public function getAlertsByType(string $type): array
    {
        return array_filter($this->getInlineAlerts(), function ($alert) use ($type) {
            return $alert->getType() === $type;
        });
    }

    /**
     * Clear alerts by type.
     */
    public function clearByType(string $type): self
    {
        $alerts = $this->getInlineAlerts();
        $filteredAlerts = array_filter($alerts, function ($alert) use ($type) {
            return $alert->getType() !== $type;
        });

        $this->session->put($this->getInlineSessionKey(), array_values($filteredAlerts));
        return $this;
    }

    /**
     * Add multiple inline alerts at once.
     */
    public function addMultiple(array $alerts): self
    {
        foreach ($alerts as $alert) {
            if (is_array($alert) && isset($alert['type'], $alert['message'])) {
                $this->addInline(
                    $alert['type'],
                    $alert['message'],
                    $alert['title'] ?? null,
                    $alert['options'] ?? []
                );
            }
        }
        return $this;
    }

    /**
     * Get the first inline alert.
     */
    public function first(): ?Alert
    {
        $alerts = $this->getInlineAlerts();
        return $alerts[0] ?? null;
    }

    /**
     * Get the last inline alert.
     */
    public function last(): ?Alert
    {
        $alerts = $this->getInlineAlerts();
        return end($alerts) ?: null;
    }

    /**
     * Remove a specific inline alert by ID.
     */
    public function removeById(string $id): self
    {
        $alerts = $this->getInlineAlerts();
        $filteredAlerts = array_filter($alerts, function ($alert) use ($id) {
            return $alert->getId() !== $id;
        });

        $this->session->put($this->getInlineSessionKey(), array_values($filteredAlerts));
        return $this;
    }

    /**
     * Flush inline alerts (clear and return them).
     */
    public function flush(): array
    {
        $alerts = $this->getInlineAlerts();
        $this->clear();
        return $alerts;
    }

    /**
     * Add inline alert with expiration time.
     */
    public function addWithExpiration(
        string $type,
        string $message,
        ?string $title = null,
        int $expiresInSeconds = 3600,
        array $options = []
    ): self {
        $options['expires_at'] = time() + $expiresInSeconds;
        return $this->addInline($type, $message, $title, $options);
    }

    /**
     * Add inline alert with auto-dismiss.
     */
    public function addWithAutoDismiss(
        string $type,
        string $message,
        ?string $title = null,
        int $autoDismissDelay = 5000,
        array $options = []
    ): self {
        $options['auto_dismiss_delay'] = $autoDismissDelay;
        return $this->addInline($type, $message, $title, $options);
    }

    /**
     * Add temporary inline alert (expires in specified seconds).
     */
    public function temporary(
        string $type,
        string $message,
        ?string $title = null,
        int $expiresInSeconds = 300,
        array $options = []
    ): self {
        return $this->addWithExpiration($type, $message, $title, $expiresInSeconds, $options);
    }

    /**
     * Add flash inline alert (auto-dismisses after delay).
     */
    public function flash(
        string $type,
        string $message,
        ?string $title = null,
        int $autoDismissDelay = 3000,
        array $options = []
    ): self {
        return $this->addWithAutoDismiss($type, $message, $title, $autoDismissDelay, $options);
    }

    /**
     * Clean up expired inline alerts.
     */
    public function cleanupExpired(): self
    {
        $alerts = $this->session->get($this->getInlineSessionKey(), []);
        $validAlerts = array_filter($alerts, function ($alert) {
            return $alert->isValid();
        });

        $this->session->put($this->getInlineSessionKey(), array_values($validAlerts));
        return $this;
    }

    /**
     * Get expired inline alerts.
     */
    public function getExpiredAlerts(): array
    {
        $alerts = $this->session->get($this->getInlineSessionKey(), []);
        return array_filter($alerts, function ($alert) {
            return $alert->isExpired();
        });
    }

    /**
     * Get inline alerts that should auto-dismiss.
     */
    public function getAutoDismissAlerts(): array
    {
        $alerts = $this->getInlineAlerts();
        return array_filter($alerts, function ($alert) {
            return $alert->shouldAutoDismiss();
        });
    }
}
