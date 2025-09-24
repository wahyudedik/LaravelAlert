<?php

namespace Wahyudedik\LaravelAlert\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class DatabaseAlert extends Model
{
    use HasFactory;

    protected $table = 'alerts';

    protected $fillable = [
        'type',
        'message',
        'title',
        'user_id',
        'session_id',
        'alert_type',
        'theme',
        'position',
        'animation',
        'dismissible',
        'auto_dismiss',
        'auto_dismiss_delay',
        'expires_at',
        'dismissed_at',
        'read_at',
        'options',
        'data_attributes',
        'icon',
        'html_content',
        'class',
        'style',
        'context',
        'field',
        'form',
        'priority',
        'is_active'
    ];

    protected $casts = [
        'dismissible' => 'boolean',
        'auto_dismiss' => 'boolean',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'dismissed_at' => 'datetime',
        'read_at' => 'datetime',
        'options' => 'array',
        'data_attributes' => 'array',
        'auto_dismiss_delay' => 'integer',
        'priority' => 'integer'
    ];

    /**
     * Get the user that owns the alert.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\\Models\\User'), 'user_id');
    }

    /**
     * Scope a query to only include active alerts.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include non-expired alerts.
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope a query to only include non-dismissed alerts.
     */
    public function scopeNotDismissed(Builder $query): Builder
    {
        return $query->whereNull('dismissed_at');
    }

    /**
     * Scope a query to only include unread alerts.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include alerts for a specific user.
     */
    public function scopeForUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include alerts for a specific session.
     */
    public function scopeForSession(Builder $query, $sessionId): Builder
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope a query to only include alerts of a specific type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include alerts of a specific alert type.
     */
    public function scopeOfAlertType(Builder $query, string $alertType): Builder
    {
        return $query->where('alert_type', $alertType);
    }

    /**
     * Scope a query to only include alerts with a specific theme.
     */
    public function scopeWithTheme(Builder $query, string $theme): Builder
    {
        return $query->where('theme', $theme);
    }

    /**
     * Scope a query to only include alerts with a specific context.
     */
    public function scopeWithContext(Builder $query, string $context): Builder
    {
        return $query->where('context', $context);
    }

    /**
     * Scope a query to only include alerts for a specific field.
     */
    public function scopeForField(Builder $query, string $field): Builder
    {
        return $query->where('field', $field);
    }

    /**
     * Scope a query to only include alerts for a specific form.
     */
    public function scopeForForm(Builder $query, string $form): Builder
    {
        return $query->where('form', $form);
    }

    /**
     * Scope a query to only include alerts with high priority.
     */
    public function scopeHighPriority(Builder $query): Builder
    {
        return $query->where('priority', '>', 0);
    }

    /**
     * Scope a query to only include alerts that should auto-dismiss.
     */
    public function scopeAutoDismiss(Builder $query): Builder
    {
        return $query->where('auto_dismiss', true);
    }

    /**
     * Scope a query to only include alerts that are dismissible.
     */
    public function scopeDismissible(Builder $query): Builder
    {
        return $query->where('dismissible', true);
    }

    /**
     * Check if the alert is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the alert is dismissed.
     */
    public function isDismissed(): bool
    {
        return !is_null($this->dismissed_at);
    }

    /**
     * Check if the alert is read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if the alert is active.
     */
    public function isActive(): bool
    {
        return $this->is_active && !$this->isExpired() && !$this->isDismissed();
    }

    /**
     * Check if the alert should auto-dismiss.
     */
    public function shouldAutoDismiss(): bool
    {
        return $this->auto_dismiss && $this->auto_dismiss_delay > 0;
    }

    /**
     * Dismiss the alert.
     */
    public function dismiss(): bool
    {
        return $this->update(['dismissed_at' => now()]);
    }

    /**
     * Mark the alert as read.
     */
    public function markAsRead(): bool
    {
        return $this->update(['read_at' => now()]);
    }

    /**
     * Mark the alert as unread.
     */
    public function markAsUnread(): bool
    {
        return $this->update(['read_at' => null]);
    }

    /**
     * Activate the alert.
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the alert.
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Set expiration time.
     */
    public function setExpiration(int $seconds): bool
    {
        return $this->update(['expires_at' => now()->addSeconds($seconds)]);
    }

    /**
     * Set auto-dismiss delay.
     */
    public function setAutoDismissDelay(int $milliseconds): bool
    {
        return $this->update([
            'auto_dismiss' => true,
            'auto_dismiss_delay' => $milliseconds
        ]);
    }

    /**
     * Get the alert's options.
     */
    public function getOptions(): array
    {
        return $this->options ?? [];
    }

    /**
     * Get a specific option.
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Set an option.
     */
    public function setOption(string $key, $value): bool
    {
        $options = $this->options ?? [];
        $options[$key] = $value;
        return $this->update(['options' => $options]);
    }

    /**
     * Get the alert's data attributes.
     */
    public function getDataAttributes(): array
    {
        return $this->data_attributes ?? [];
    }

    /**
     * Get a specific data attribute.
     */
    public function getDataAttribute(string $key, $default = null)
    {
        return $this->data_attributes[$key] ?? $default;
    }

    /**
     * Set a data attribute.
     */
    public function setDataAttribute(string $key, $value): bool
    {
        $attributes = $this->data_attributes ?? [];
        $attributes[$key] = $value;
        return $this->update(['data_attributes' => $attributes]);
    }

    /**
     * Get the alert's HTML content.
     */
    public function getHtmlContent(): ?string
    {
        return $this->html_content;
    }

    /**
     * Set the alert's HTML content.
     */
    public function setHtmlContent(string $content): bool
    {
        return $this->update(['html_content' => $content]);
    }

    /**
     * Get the alert's icon.
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Set the alert's icon.
     */
    public function setIcon(string $icon): bool
    {
        return $this->update(['icon' => $icon]);
    }

    /**
     * Get the alert's CSS classes.
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * Set the alert's CSS classes.
     */
    public function setClass(string $class): bool
    {
        return $this->update(['class' => $class]);
    }

    /**
     * Get the alert's styles.
     */
    public function getStyle(): ?string
    {
        return $this->style;
    }

    /**
     * Set the alert's styles.
     */
    public function setStyle(string $style): bool
    {
        return $this->update(['style' => $style]);
    }

    /**
     * Get the alert's priority.
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Set the alert's priority.
     */
    public function setPriority(int $priority): bool
    {
        return $this->update(['priority' => $priority]);
    }

    /**
     * Get the alert's context.
     */
    public function getContext(): ?string
    {
        return $this->context;
    }

    /**
     * Set the alert's context.
     */
    public function setContext(string $context): bool
    {
        return $this->update(['context' => $context]);
    }

    /**
     * Get the alert's field.
     */
    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * Set the alert's field.
     */
    public function setField(string $field): bool
    {
        return $this->update(['field' => $field]);
    }

    /**
     * Get the alert's form.
     */
    public function getForm(): ?string
    {
        return $this->form;
    }

    /**
     * Set the alert's form.
     */
    public function setForm(string $form): bool
    {
        return $this->update(['form' => $form]);
    }

    /**
     * Get the alert's animation.
     */
    public function getAnimation(): ?string
    {
        return $this->animation;
    }

    /**
     * Set the alert's animation.
     */
    public function setAnimation(string $animation): bool
    {
        return $this->update(['animation' => $animation]);
    }

    /**
     * Get the alert's position.
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * Set the alert's position.
     */
    public function setPosition(string $position): bool
    {
        return $this->update(['position' => $position]);
    }

    /**
     * Get the alert's theme.
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * Set the alert's theme.
     */
    public function setTheme(string $theme): bool
    {
        return $this->update(['theme' => $theme]);
    }

    /**
     * Get the alert's auto-dismiss delay.
     */
    public function getAutoDismissDelay(): ?int
    {
        return $this->auto_dismiss_delay;
    }

    /**
     * Get the alert's expiration time.
     */
    public function getExpiresAt(): ?Carbon
    {
        return $this->expires_at;
    }

    /**
     * Get the alert's dismissal time.
     */
    public function getDismissedAt(): ?Carbon
    {
        return $this->dismissed_at;
    }

    /**
     * Get the alert's read time.
     */
    public function getReadAt(): ?Carbon
    {
        return $this->read_at;
    }

    /**
     * Get the alert's creation time.
     */
    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    /**
     * Get the alert's update time.
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updated_at;
    }

    /**
     * Get the alert's ID.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the alert's type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the alert's message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the alert's title.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Get the alert's user ID.
     */
    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    /**
     * Get the alert's session ID.
     */
    public function getSessionId(): ?string
    {
        return $this->session_id;
    }

    /**
     * Get the alert's alert type.
     */
    public function getAlertType(): string
    {
        return $this->alert_type;
    }

    /**
     * Check if the alert is dismissible.
     */
    public function isDismissible(): bool
    {
        return $this->dismissible;
    }

    /**
     * Check if the alert has auto-dismiss enabled.
     */
    public function hasAutoDismiss(): bool
    {
        return $this->auto_dismiss;
    }

    /**
     * Get the alert's priority level.
     */
    public function getPriorityLevel(): string
    {
        if ($this->priority >= 3) return 'high';
        if ($this->priority >= 1) return 'medium';
        return 'low';
    }

    /**
     * Get the alert's status.
     */
    public function getStatus(): string
    {
        if ($this->isDismissed()) return 'dismissed';
        if ($this->isExpired()) return 'expired';
        if ($this->isRead()) return 'read';
        if ($this->isActive()) return 'active';
        return 'inactive';
    }

    /**
     * Get the alert's age in human-readable format.
     */
    public function getAge(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the alert's time until expiration.
     */
    public function getTimeUntilExpiration(): ?string
    {
        if (!$this->expires_at) return null;
        return $this->expires_at->diffForHumans();
    }

    /**
     * Get the alert's time since dismissal.
     */
    public function getTimeSinceDismissal(): ?string
    {
        if (!$this->dismissed_at) return null;
        return $this->dismissed_at->diffForHumans();
    }

    /**
     * Get the alert's time since read.
     */
    public function getTimeSinceRead(): ?string
    {
        if (!$this->read_at) return null;
        return $this->read_at->diffForHumans();
    }
}
