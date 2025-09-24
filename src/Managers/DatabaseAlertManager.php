<?php

namespace Wahyudedik\LaravelAlert\Managers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Wahyudedik\LaravelAlert\Models\DatabaseAlert;
use Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface;

class DatabaseAlertManager implements AlertManagerInterface
{
    protected array $config;

    public function __construct()
    {
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
        $alertData = [
            'type' => $type,
            'message' => $message,
            'title' => $title,
            'user_id' => Auth::id(),
            'session_id' => Session::getId(),
            'alert_type' => $options['alert_type'] ?? 'alert',
            'theme' => $options['theme'] ?? $this->config['default_theme'] ?? 'bootstrap',
            'position' => $options['position'] ?? $this->config['position'] ?? 'top-right',
            'animation' => $options['animation'] ?? $this->config['animation'] ?? 'fade',
            'dismissible' => $options['dismissible'] ?? true,
            'auto_dismiss' => $options['auto_dismiss'] ?? false,
            'auto_dismiss_delay' => $options['auto_dismiss_delay'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
            'options' => $options,
            'data_attributes' => $options['data_attributes'] ?? null,
            'icon' => $options['icon'] ?? null,
            'html_content' => $options['html_content'] ?? null,
            'class' => $options['class'] ?? null,
            'style' => $options['style'] ?? null,
            'context' => $options['context'] ?? null,
            'field' => $options['field'] ?? null,
            'form' => $options['form'] ?? null,
            'priority' => $options['priority'] ?? 0,
            'is_active' => true
        ];

        DatabaseAlert::create($alertData);

        return $this;
    }

    /**
     * Get all alerts.
     */
    public function getAlerts(): array
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->when(Auth::check(), function ($query) {
                return $query->where(function ($q) {
                    $q->where('user_id', Auth::id())
                        ->orWhere('user_id', null);
                });
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Clear all alerts.
     */
    public function clear(): self
    {
        DatabaseAlert::active()
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->update(['is_active' => false]);

        return $this;
    }

    /**
     * Render a single alert.
     */
    public function render(string $type, string $message, ?string $title = null, array $options = []): string
    {
        $alert = $this->add($type, $message, $title, $options);
        $alerts = $this->getAlerts();
        $latestAlert = $alerts[0] ?? null;

        if (!$latestAlert) {
            return '';
        }

        return $this->renderAlert($latestAlert);
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

        return $html;
    }

    /**
     * Render a single alert object.
     */
    protected function renderAlert(array $alert): string
    {
        $theme = $alert['theme'] ?? 'bootstrap';
        $view = "laravel-alert::components.{$theme}.alert";

        return view($view, [
            'alert' => (object) $alert,
            'config' => $this->config,
        ])->render();
    }

    /**
     * Get alerts count.
     */
    public function count(): int
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->count();
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
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->ofType($type)
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Clear alerts by type.
     */
    public function clearByType(string $type): self
    {
        DatabaseAlert::active()
            ->ofType($type)
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->update(['is_active' => false]);

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
    public function first(): ?DatabaseAlert
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get the last alert.
     */
    public function last(): ?DatabaseAlert
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Remove a specific alert by ID.
     */
    public function removeById(string $id): self
    {
        DatabaseAlert::where('id', $id)
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->update(['is_active' => false]);

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

    /**
     * Add alert with expiration time.
     */
    public function addWithExpiration(
        string $type,
        string $message,
        ?string $title = null,
        int $expiresInSeconds = 3600,
        array $options = []
    ): self {
        $options['expires_at'] = now()->addSeconds($expiresInSeconds);
        return $this->add($type, $message, $title, $options);
    }

    /**
     * Add alert with auto-dismiss.
     */
    public function addWithAutoDismiss(
        string $type,
        string $message,
        ?string $title = null,
        int $autoDismissDelay = 5000,
        array $options = []
    ): self {
        $options['auto_dismiss'] = true;
        $options['auto_dismiss_delay'] = $autoDismissDelay;
        return $this->add($type, $message, $title, $options);
    }

    /**
     * Add temporary alert (expires in specified seconds).
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
     * Add flash alert (auto-dismisses after delay).
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
     * Clean up expired alerts.
     */
    public function cleanupExpired(): self
    {
        DatabaseAlert::where('expires_at', '<', now())
            ->update(['is_active' => false]);

        return $this;
    }

    /**
     * Get expired alerts.
     */
    public function getExpiredAlerts(): array
    {
        return DatabaseAlert::where('expires_at', '<', now())
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->get()
            ->toArray();
    }

    /**
     * Get alerts that should auto-dismiss.
     */
    public function getAutoDismissAlerts(): array
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->autoDismiss()
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->get()
            ->toArray();
    }

    /**
     * Get alerts for a specific user.
     */
    public function getAlertsForUser(int $userId): array
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->forUser($userId)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get alerts for a specific session.
     */
    public function getAlertsForSession(string $sessionId): array
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->forSession($sessionId)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get alerts by context.
     */
    public function getAlertsByContext(string $context): array
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->withContext($context)
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get alerts by field.
     */
    public function getAlertsByField(string $field): array
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->forField($field)
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get alerts by form.
     */
    public function getAlertsByForm(string $form): array
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->forForm($form)
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get high priority alerts.
     */
    public function getHighPriorityAlerts(): array
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->highPriority()
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get alert statistics.
     */
    public function getStats(): array
    {
        $baseQuery = DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            });

        return [
            'total' => $baseQuery->count(),
            'success' => $baseQuery->clone()->ofType('success')->count(),
            'error' => $baseQuery->clone()->ofType('error')->count(),
            'warning' => $baseQuery->clone()->ofType('warning')->count(),
            'info' => $baseQuery->clone()->ofType('info')->count(),
            'high_priority' => $baseQuery->clone()->highPriority()->count(),
            'auto_dismiss' => $baseQuery->clone()->autoDismiss()->count(),
            'dismissible' => $baseQuery->clone()->dismissible()->count()
        ];
    }

    /**
     * Get alert history.
     */
    public function getHistory(int $limit = 50): array
    {
        return DatabaseAlert::when(Auth::check(), function ($query) {
            return $query->where('user_id', Auth::id());
        })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Mark alert as read.
     */
    public function markAsRead(int $alertId): bool
    {
        $alert = DatabaseAlert::where('id', $alertId)
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->first();

        if ($alert) {
            return $alert->markAsRead();
        }

        return false;
    }

    /**
     * Mark all alerts as read.
     */
    public function markAllAsRead(): bool
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->update(['read_at' => now()]);
    }

    /**
     * Dismiss alert.
     */
    public function dismiss(int $alertId): bool
    {
        $alert = DatabaseAlert::where('id', $alertId)
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->first();

        if ($alert) {
            return $alert->dismiss();
        }

        return false;
    }

    /**
     * Dismiss all alerts.
     */
    public function dismissAll(): bool
    {
        return DatabaseAlert::active()
            ->notExpired()
            ->notDismissed()
            ->when(Auth::check(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->when(!Auth::check(), function ($query) {
                return $query->where('session_id', Session::getId());
            })
            ->update(['dismissed_at' => now()]);
    }
}
