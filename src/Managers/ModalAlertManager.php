<?php

namespace Wahyudedik\LaravelAlert\Managers;

use Illuminate\Session\Store;
use Wahyudedik\LaravelAlert\Models\Alert;
use Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface;

class ModalAlertManager implements AlertManagerInterface
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
     * Add a success modal.
     */
    public function success(string $message, ?string $title = null, array $options = []): self
    {
        return $this->addModal('success', $message, $title, $options);
    }

    /**
     * Add an error modal.
     */
    public function error(string $message, ?string $title = null, array $options = []): self
    {
        return $this->addModal('error', $message, $title, $options);
    }

    /**
     * Add a warning modal.
     */
    public function warning(string $message, ?string $title = null, array $options = []): self
    {
        return $this->addModal('warning', $message, $title, $options);
    }

    /**
     * Add an info modal.
     */
    public function info(string $message, ?string $title = null, array $options = []): self
    {
        return $this->addModal('info', $message, $title, $options);
    }

    /**
     * Add a modal with custom type.
     */
    public function add(string $type, string $message, ?string $title = null, array $options = []): self
    {
        return $this->addModal($type, $message, $title, $options);
    }

    /**
     * Add a modal alert.
     */
    public function addModal(string $type, string $message, ?string $title = null, array $options = []): self
    {
        $defaultOptions = [
            'modal' => true,
            'backdrop' => $this->config['modal_backdrop'] ?? true,
            'keyboard' => $this->config['modal_keyboard'] ?? true,
            'focus' => $this->config['modal_focus'] ?? true,
            'size' => $this->config['modal_size'] ?? 'md',
            'dismissible' => false, // Modals are not auto-dismissible by default
            'actions' => $this->getDefaultActions($type),
            'show_close_button' => true,
            'centered' => $this->config['modal_centered'] ?? true,
        ];

        $options = array_merge($defaultOptions, $options);

        $alert = new Alert($type, $message, $title, $options);

        $modals = $this->getModals();
        $modals[] = $alert;

        // Limit the number of modals (usually 1 at a time)
        $maxModals = $options['max_modals'] ?? 1;
        if (count($modals) > $maxModals) {
            $modals = array_slice($modals, -$maxModals);
        }

        $this->session->put($this->getModalSessionKey(), $modals);

        return $this;
    }

    /**
     * Add a confirmation modal.
     */
    public function confirm(
        string $message,
        ?string $title = null,
        array $actions = [],
        array $options = []
    ): self {
        $defaultActions = [
            'confirm' => [
                'text' => 'Confirm',
                'class' => 'btn-primary',
                'action' => 'confirm'
            ],
            'cancel' => [
                'text' => 'Cancel',
                'class' => 'btn-secondary',
                'action' => 'cancel'
            ]
        ];

        $actions = array_merge($defaultActions, $actions);
        $options['actions'] = $actions;
        $options['type'] = 'confirm';

        return $this->addModal('warning', $message, $title, $options);
    }

    /**
     * Add a prompt modal.
     */
    public function prompt(
        string $message,
        ?string $title = null,
        array $actions = [],
        array $options = []
    ): self {
        $defaultActions = [
            'submit' => [
                'text' => 'Submit',
                'class' => 'btn-primary',
                'action' => 'submit'
            ],
            'cancel' => [
                'text' => 'Cancel',
                'class' => 'btn-secondary',
                'action' => 'cancel'
            ]
        ];

        $actions = array_merge($defaultActions, $actions);
        $options['actions'] = $actions;
        $options['type'] = 'prompt';
        $options['input'] = true;

        return $this->addModal('info', $message, $title, $options);
    }

    /**
     * Get all modals.
     */
    public function getAlerts(): array
    {
        return $this->getModals();
    }

    /**
     * Get all modals.
     */
    public function getModals(): array
    {
        $modals = $this->session->get($this->getModalSessionKey(), []);

        // Filter out expired modals
        $validModals = array_filter($modals, function ($modal) {
            return $modal->isValid();
        });

        // Update session with valid modals only
        if (count($validModals) !== count($modals)) {
            $this->session->put($this->getModalSessionKey(), array_values($validModals));
        }

        return $validModals;
    }

    /**
     * Clear all modals.
     */
    public function clear(): self
    {
        $this->session->forget($this->getModalSessionKey());
        return $this;
    }

    /**
     * Render a single modal.
     */
    public function render(string $type, string $message, ?string $title = null, array $options = []): string
    {
        $alert = new Alert($type, $message, $title, $options);
        return $this->renderModal($alert);
    }

    /**
     * Render all modals.
     */
    public function renderAll(): string
    {
        $modals = $this->getModals();
        $html = '';

        foreach ($modals as $modal) {
            $html .= $this->renderModal($modal);
        }

        // Clear modals after rendering
        $this->clear();

        return $html;
    }

    /**
     * Render a single modal object.
     */
    protected function renderModal(Alert $modal): string
    {
        $theme = $this->config['default_theme'] ?? 'bootstrap';
        $view = "laravel-alert::modals.{$theme}.modal";

        return view($view, [
            'modal' => $modal,
            'config' => $this->config,
        ])->render();
    }

    /**
     * Get the session key for modals.
     */
    protected function getModalSessionKey(): string
    {
        return $this->sessionKey . '_modals';
    }

    /**
     * Get default actions for modal type.
     */
    protected function getDefaultActions(string $type): array
    {
        switch ($type) {
            case 'success':
                return [
                    'ok' => [
                        'text' => 'OK',
                        'class' => 'btn-success',
                        'action' => 'close'
                    ]
                ];
            case 'error':
                return [
                    'ok' => [
                        'text' => 'OK',
                        'class' => 'btn-danger',
                        'action' => 'close'
                    ]
                ];
            case 'warning':
                return [
                    'ok' => [
                        'text' => 'OK',
                        'class' => 'btn-warning',
                        'action' => 'close'
                    ]
                ];
            default:
                return [
                    'ok' => [
                        'text' => 'OK',
                        'class' => 'btn-primary',
                        'action' => 'close'
                    ]
                ];
        }
    }

    /**
     * Get modals count.
     */
    public function count(): int
    {
        return count($this->getModals());
    }

    /**
     * Check if there are any modals.
     */
    public function hasAlerts(): bool
    {
        return $this->count() > 0;
    }

    /**
     * Get modals by type.
     */
    public function getAlertsByType(string $type): array
    {
        return array_filter($this->getModals(), function ($modal) use ($type) {
            return $modal->getType() === $type;
        });
    }

    /**
     * Clear modals by type.
     */
    public function clearByType(string $type): self
    {
        $modals = $this->getModals();
        $filteredModals = array_filter($modals, function ($modal) use ($type) {
            return $modal->getType() !== $type;
        });

        $this->session->put($this->getModalSessionKey(), array_values($filteredModals));
        return $this;
    }

    /**
     * Add multiple modals at once.
     */
    public function addMultiple(array $modals): self
    {
        foreach ($modals as $modal) {
            if (is_array($modal) && isset($modal['type'], $modal['message'])) {
                $this->addModal(
                    $modal['type'],
                    $modal['message'],
                    $modal['title'] ?? null,
                    $modal['options'] ?? []
                );
            }
        }
        return $this;
    }

    /**
     * Get the first modal.
     */
    public function first(): ?Alert
    {
        $modals = $this->getModals();
        return $modals[0] ?? null;
    }

    /**
     * Get the last modal.
     */
    public function last(): ?Alert
    {
        $modals = $this->getModals();
        return end($modals) ?: null;
    }

    /**
     * Remove a specific modal by ID.
     */
    public function removeById(string $id): self
    {
        $modals = $this->getModals();
        $filteredModals = array_filter($modals, function ($modal) use ($id) {
            return $modal->getId() !== $id;
        });

        $this->session->put($this->getModalSessionKey(), array_values($filteredModals));
        return $this;
    }

    /**
     * Flush modals (clear and return them).
     */
    public function flush(): array
    {
        $modals = $this->getModals();
        $this->clear();
        return $modals;
    }

    /**
     * Add modal with expiration time.
     */
    public function addWithExpiration(
        string $type,
        string $message,
        ?string $title = null,
        int $expiresInSeconds = 3600,
        array $options = []
    ): self {
        $options['expires_at'] = time() + $expiresInSeconds;
        return $this->addModal($type, $message, $title, $options);
    }

    /**
     * Add modal with auto-dismiss.
     */
    public function addWithAutoDismiss(
        string $type,
        string $message,
        ?string $title = null,
        int $autoDismissDelay = 5000,
        array $options = []
    ): self {
        $options['auto_dismiss_delay'] = $autoDismissDelay;
        return $this->addModal($type, $message, $title, $options);
    }

    /**
     * Add temporary modal (expires in specified seconds).
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
     * Add flash modal (auto-dismisses after delay).
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
     * Clean up expired modals.
     */
    public function cleanupExpired(): self
    {
        $modals = $this->session->get($this->getModalSessionKey(), []);
        $validModals = array_filter($modals, function ($modal) {
            return $modal->isValid();
        });

        $this->session->put($this->getModalSessionKey(), array_values($validModals));
        return $this;
    }

    /**
     * Get expired modals.
     */
    public function getExpiredAlerts(): array
    {
        $modals = $this->session->get($this->getModalSessionKey(), []);
        return array_filter($modals, function ($modal) {
            return $modal->isExpired();
        });
    }

    /**
     * Get modals that should auto-dismiss.
     */
    public function getAutoDismissAlerts(): array
    {
        $modals = $this->getModals();
        return array_filter($modals, function ($modal) {
            return $modal->shouldAutoDismiss();
        });
    }
}
