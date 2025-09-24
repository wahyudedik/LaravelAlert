<?php

namespace Wahyudedik\LaravelAlert\Managers;

use Illuminate\Session\Store;
use Wahyudedik\LaravelAlert\Models\Alert;
use Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface;

class ToastAlertManager implements AlertManagerInterface
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
     * Add a success toast.
     */
    public function success(string $message, ?string $title = null, array $options = []): self
    {
        return $this->addToast('success', $message, $title, $options);
    }

    /**
     * Add an error toast.
     */
    public function error(string $message, ?string $title = null, array $options = []): self
    {
        return $this->addToast('error', $message, $title, $options);
    }

    /**
     * Add a warning toast.
     */
    public function warning(string $message, ?string $title = null, array $options = []): self
    {
        return $this->addToast('warning', $message, $title, $options);
    }

    /**
     * Add an info toast.
     */
    public function info(string $message, ?string $title = null, array $options = []): self
    {
        return $this->addToast('info', $message, $title, $options);
    }

    /**
     * Add a toast with custom type.
     */
    public function add(string $type, string $message, ?string $title = null, array $options = []): self
    {
        return $this->addToast($type, $message, $title, $options);
    }

    /**
     * Add a toast notification.
     */
    public function addToast(string $type, string $message, ?string $title = null, array $options = []): self
    {
        $defaultOptions = [
            'toast' => true,
            'position' => $this->config['toast_position'] ?? 'top-right',
            'auto_dismiss_delay' => $this->config['toast_auto_dismiss_delay'] ?? 5000,
            'animation' => $this->config['toast_animation'] ?? 'slide',
            'dismissible' => true,
            'show_progress' => true,
            'stack' => true,
            'max_toasts' => $this->config['toast_max_toasts'] ?? 5,
        ];

        $options = array_merge($defaultOptions, $options);

        $alert = new Alert($type, $message, $title, $options);

        $toasts = $this->getToasts();
        $toasts[] = $alert;

        // Limit the number of toasts
        $maxToasts = $options['max_toasts'] ?? $this->config['toast_max_toasts'] ?? 5;
        if (count($toasts) > $maxToasts) {
            $toasts = array_slice($toasts, -$maxToasts);
        }

        $this->session->put($this->getToastSessionKey(), $toasts);

        return $this;
    }

    /**
     * Get all toasts.
     */
    public function getAlerts(): array
    {
        return $this->getToasts();
    }

    /**
     * Get all toasts.
     */
    public function getToasts(): array
    {
        $toasts = $this->session->get($this->getToastSessionKey(), []);

        // Filter out expired toasts
        $validToasts = array_filter($toasts, function ($toast) {
            return $toast->isValid();
        });

        // Update session with valid toasts only
        if (count($validToasts) !== count($toasts)) {
            $this->session->put($this->getToastSessionKey(), array_values($validToasts));
        }

        return $validToasts;
    }

    /**
     * Clear all toasts.
     */
    public function clear(): self
    {
        $this->session->forget($this->getToastSessionKey());
        return $this;
    }

    /**
     * Render a single toast.
     */
    public function render(string $type, string $message, ?string $title = null, array $options = []): string
    {
        $alert = new Alert($type, $message, $title, $options);
        return $this->renderToast($alert);
    }

    /**
     * Render all toasts.
     */
    public function renderAll(): string
    {
        $toasts = $this->getToasts();
        $html = '';

        foreach ($toasts as $toast) {
            $html .= $this->renderToast($toast);
        }

        // Clear toasts after rendering
        $this->clear();

        return $html;
    }

    /**
     * Render a single toast object.
     */
    protected function renderToast(Alert $toast): string
    {
        $theme = $this->config['default_theme'] ?? 'bootstrap';
        $view = "laravel-alert::toasts.{$theme}.toast";

        return view($view, [
            'toast' => $toast,
            'config' => $this->config,
        ])->render();
    }

    /**
     * Get the session key for toasts.
     */
    protected function getToastSessionKey(): string
    {
        return $this->sessionKey . '_toasts';
    }

    /**
     * Get toasts count.
     */
    public function count(): int
    {
        return count($this->getToasts());
    }

    /**
     * Check if there are any toasts.
     */
    public function hasAlerts(): bool
    {
        return $this->count() > 0;
    }

    /**
     * Get toasts by type.
     */
    public function getAlertsByType(string $type): array
    {
        return array_filter($this->getToasts(), function ($toast) use ($type) {
            return $toast->getType() === $type;
        });
    }

    /**
     * Clear toasts by type.
     */
    public function clearByType(string $type): self
    {
        $toasts = $this->getToasts();
        $filteredToasts = array_filter($toasts, function ($toast) use ($type) {
            return $toast->getType() !== $type;
        });

        $this->session->put($this->getToastSessionKey(), array_values($filteredToasts));
        return $this;
    }

    /**
     * Add multiple toasts at once.
     */
    public function addMultiple(array $toasts): self
    {
        foreach ($toasts as $toast) {
            if (is_array($toast) && isset($toast['type'], $toast['message'])) {
                $this->addToast(
                    $toast['type'],
                    $toast['message'],
                    $toast['title'] ?? null,
                    $toast['options'] ?? []
                );
            }
        }
        return $this;
    }

    /**
     * Get the first toast.
     */
    public function first(): ?Alert
    {
        $toasts = $this->getToasts();
        return $toasts[0] ?? null;
    }

    /**
     * Get the last toast.
     */
    public function last(): ?Alert
    {
        $toasts = $this->getToasts();
        return end($toasts) ?: null;
    }

    /**
     * Remove a specific toast by ID.
     */
    public function removeById(string $id): self
    {
        $toasts = $this->getToasts();
        $filteredToasts = array_filter($toasts, function ($toast) use ($id) {
            return $toast->getId() !== $id;
        });

        $this->session->put($this->getToastSessionKey(), array_values($filteredToasts));
        return $this;
    }

    /**
     * Flush toasts (clear and return them).
     */
    public function flush(): array
    {
        $toasts = $this->getToasts();
        $this->clear();
        return $toasts;
    }

    /**
     * Add toast with expiration time.
     */
    public function addWithExpiration(
        string $type,
        string $message,
        ?string $title = null,
        int $expiresInSeconds = 3600,
        array $options = []
    ): self {
        $options['expires_at'] = time() + $expiresInSeconds;
        return $this->addToast($type, $message, $title, $options);
    }

    /**
     * Add toast with auto-dismiss.
     */
    public function addWithAutoDismiss(
        string $type,
        string $message,
        ?string $title = null,
        int $autoDismissDelay = 5000,
        array $options = []
    ): self {
        $options['auto_dismiss_delay'] = $autoDismissDelay;
        return $this->addToast($type, $message, $title, $options);
    }

    /**
     * Add temporary toast (expires in specified seconds).
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
     * Add flash toast (auto-dismisses after delay).
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
     * Clean up expired toasts.
     */
    public function cleanupExpired(): self
    {
        $toasts = $this->session->get($this->getToastSessionKey(), []);
        $validToasts = array_filter($toasts, function ($toast) {
            return $toast->isValid();
        });

        $this->session->put($this->getToastSessionKey(), array_values($validToasts));
        return $this;
    }

    /**
     * Get expired toasts.
     */
    public function getExpiredAlerts(): array
    {
        $toasts = $this->session->get($this->getToastSessionKey(), []);
        return array_filter($toasts, function ($toast) {
            return $toast->isExpired();
        });
    }

    /**
     * Get toasts that should auto-dismiss.
     */
    public function getAutoDismissAlerts(): array
    {
        $toasts = $this->getToasts();
        return array_filter($toasts, function ($toast) {
            return $toast->shouldAutoDismiss();
        });
    }
}
