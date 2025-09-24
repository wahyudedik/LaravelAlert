<?php

namespace Wahyudedik\LaravelAlert\Managers;

use Illuminate\Session\Store;
use Wahyudedik\LaravelAlert\Models\Alert;
use Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface;

class AlertManager implements AlertManagerInterface
{
    protected Store $session;
    protected array $config;

    public function __construct(Store $session)
    {
        $this->session = $session;
        $this->config = config('laravel-alert', []);
    }

    /**
     * Add a success alert.
     */
    public function success(string $message, ?string $title = null, array $options = []): self
    {
        return $this->add('success', $message, $title, $options);
    }

    /**
     * Add an error alert.
     */
    public function error(string $message, ?string $title = null, array $options = []): self
    {
        return $this->add('error', $message, $title, $options);
    }

    /**
     * Add a warning alert.
     */
    public function warning(string $message, ?string $title = null, array $options = []): self
    {
        return $this->add('warning', $message, $title, $options);
    }

    /**
     * Add an info alert.
     */
    public function info(string $message, ?string $title = null, array $options = []): self
    {
        return $this->add('info', $message, $title, $options);
    }

    /**
     * Add an alert with custom type.
     */
    public function add(string $type, string $message, ?string $title = null, array $options = []): self
    {
        $alert = new Alert($type, $message, $title, $options);

        $alerts = $this->getAlerts();
        $alerts[] = $alert;

        // Limit the number of alerts
        $maxAlerts = $this->config['max_alerts'] ?? 5;
        if (count($alerts) > $maxAlerts) {
            $alerts = array_slice($alerts, -$maxAlerts);
        }

        $this->session->put($this->getSessionKey(), $alerts);

        return $this;
    }

    /**
     * Get all alerts.
     */
    public function getAlerts(): array
    {
        return $this->session->get($this->getSessionKey(), []);
    }

    /**
     * Clear all alerts.
     */
    public function clear(): self
    {
        $this->session->forget($this->getSessionKey());
        return $this;
    }

    /**
     * Render a single alert.
     */
    public function render(string $type, string $message, ?string $title = null, array $options = []): string
    {
        $alert = new Alert($type, $message, $title, $options);
        return $this->renderAlert($alert);
    }

    /**
     * Render all alerts.
     */
    public function renderAll(): string
    {
        $alerts = $this->getAlerts();
        $html = '';

        foreach ($alerts as $alert) {
            $html .= $this->renderAlert($alert);
        }

        // Clear alerts after rendering
        $this->clear();

        return $html;
    }

    /**
     * Render a single alert object.
     */
    protected function renderAlert(Alert $alert): string
    {
        $theme = $this->config['default_theme'] ?? 'bootstrap';
        $view = "laravel-alert::components.{$theme}.alert";

        return view($view, [
            'alert' => $alert,
            'config' => $this->config,
        ])->render();
    }

    /**
     * Get the session key for alerts.
     */
    protected function getSessionKey(): string
    {
        return $this->config['session_key'] ?? 'laravel_alerts';
    }

    /**
     * Get alerts count.
     */
    public function count(): int
    {
        return count($this->getAlerts());
    }

    /**
     * Check if there are any alerts.
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
        return array_filter($this->getAlerts(), function ($alert) use ($type) {
            return $alert->getType() === $type;
        });
    }

    /**
     * Clear alerts by type.
     */
    public function clearByType(string $type): self
    {
        $alerts = $this->getAlerts();
        $filteredAlerts = array_filter($alerts, function ($alert) use ($type) {
            return $alert->getType() !== $type;
        });

        $this->session->put($this->getSessionKey(), array_values($filteredAlerts));
        return $this;
    }

    /**
     * Add multiple alerts at once.
     */
    public function addMultiple(array $alerts): self
    {
        foreach ($alerts as $alert) {
            if (is_array($alert) && isset($alert['type'], $alert['message'])) {
                $this->add(
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
     * Get the first alert.
     */
    public function first(): ?Alert
    {
        $alerts = $this->getAlerts();
        return $alerts[0] ?? null;
    }

    /**
     * Get the last alert.
     */
    public function last(): ?Alert
    {
        $alerts = $this->getAlerts();
        return end($alerts) ?: null;
    }

    /**
     * Remove a specific alert by ID.
     */
    public function removeById(string $id): self
    {
        $alerts = $this->getAlerts();
        $filteredAlerts = array_filter($alerts, function ($alert) use ($id) {
            return $alert->getId() !== $id;
        });

        $this->session->put($this->getSessionKey(), array_values($filteredAlerts));
        return $this;
    }

    /**
     * Flush alerts (clear and return them).
     */
    public function flush(): array
    {
        $alerts = $this->getAlerts();
        $this->clear();
        return $alerts;
    }
}
