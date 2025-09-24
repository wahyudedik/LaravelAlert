<?php

namespace Wahyudedik\LaravelAlert\Contracts;

interface AlertManagerInterface
{
    /**
     * Add a success alert.
     */
    public function success(string $message, ?string $title = null, array $options = []): self;

    /**
     * Add an error alert.
     */
    public function error(string $message, ?string $title = null, array $options = []): self;

    /**
     * Add a warning alert.
     */
    public function warning(string $message, ?string $title = null, array $options = []): self;

    /**
     * Add an info alert.
     */
    public function info(string $message, ?string $title = null, array $options = []): self;

    /**
     * Add an alert with custom type.
     */
    public function add(string $type, string $message, ?string $title = null, array $options = []): self;

    /**
     * Get all alerts.
     */
    public function getAlerts(): array;

    /**
     * Clear all alerts.
     */
    public function clear(): self;

    /**
     * Render a single alert.
     */
    public function render(string $type, string $message, ?string $title = null, array $options = []): string;

    /**
     * Render all alerts.
     */
    public function renderAll(): string;

    /**
     * Get alerts count.
     */
    public function count(): int;

    /**
     * Check if there are any alerts.
     */
    public function hasAlerts(): bool;

    /**
     * Get alerts by type.
     */
    public function getAlertsByType(string $type): array;

    /**
     * Clear alerts by type.
     */
    public function clearByType(string $type): self;

    /**
     * Add multiple alerts at once.
     */
    public function addMultiple(array $alerts): self;

    /**
     * Get the first alert.
     */
    public function first(): ?\Wahyudedik\LaravelAlert\Models\Alert;

    /**
     * Get the last alert.
     */
    public function last(): ?\Wahyudedik\LaravelAlert\Models\Alert;

    /**
     * Remove a specific alert by ID.
     */
    public function removeById(string $id): self;

    /**
     * Flush alerts (clear and return them).
     */
    public function flush(): array;

    /**
     * Add alert with expiration time.
     */
    public function addWithExpiration(string $type, string $message, ?string $title = null, int $expiresInSeconds = 3600, array $options = []): self;

    /**
     * Add alert with auto-dismiss.
     */
    public function addWithAutoDismiss(string $type, string $message, ?string $title = null, int $autoDismissDelay = 5000, array $options = []): self;

    /**
     * Add temporary alert (expires in specified seconds).
     */
    public function temporary(string $type, string $message, ?string $title = null, int $expiresInSeconds = 300, array $options = []): self;

    /**
     * Add flash alert (auto-dismisses after delay).
     */
    public function flash(string $type, string $message, ?string $title = null, int $autoDismissDelay = 3000, array $options = []): self;

    /**
     * Clean up expired alerts.
     */
    public function cleanupExpired(): self;

    /**
     * Get expired alerts.
     */
    public function getExpiredAlerts(): array;

    /**
     * Get alerts that should auto-dismiss.
     */
    public function getAutoDismissAlerts(): array;
}
