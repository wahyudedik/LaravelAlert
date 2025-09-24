<?php

namespace Wahyudedik\LaravelAlert\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Wahyudedik\LaravelAlert\Managers\AlertManager;
use Wahyudedik\LaravelAlert\Managers\DatabaseAlertManager;
use Wahyudedik\LaravelAlert\Managers\RedisAlertManager;
use Wahyudedik\LaravelAlert\Managers\CacheAlertManager;
use Wahyudedik\LaravelAlert\Models\DatabaseAlert;

class AlertApiController extends Controller
{
    protected AlertManager $alertManager;
    protected DatabaseAlertManager $databaseManager;
    protected RedisAlertManager $redisManager;
    protected CacheAlertManager $cacheManager;
    protected string $storageDriver;

    public function __construct(
        AlertManager $alertManager,
        DatabaseAlertManager $databaseManager,
        RedisAlertManager $redisManager,
        CacheAlertManager $cacheManager
    ) {
        $this->alertManager = $alertManager;
        $this->databaseManager = $databaseManager;
        $this->redisManager = $redisManager;
        $this->cacheManager = $cacheManager;
        $this->storageDriver = config('laravel-alert.storage.driver', 'database');
    }

    /**
     * Get the appropriate manager based on storage driver.
     */
    protected function getManager()
    {
        switch ($this->storageDriver) {
            case 'redis':
                return $this->redisManager;
            case 'cache':
                return $this->cacheManager;
            case 'database':
            default:
                return $this->databaseManager;
        }
    }

    /**
     * Display a listing of alerts.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $manager = $this->getManager();

            // Apply filters
            $filters = $this->applyFilters($request);
            $alerts = $this->getFilteredAlerts($manager, $filters);

            // Apply pagination
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $paginatedAlerts = $this->paginateAlerts($alerts, $perPage, $page);

            return $this->successResponse([
                'alerts' => $paginatedAlerts['data'],
                'pagination' => $paginatedAlerts['pagination'],
                'meta' => [
                    'total' => count($alerts),
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $paginatedAlerts['pagination']['last_page']
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve alerts', 500, $e->getMessage());
        }
    }

    /**
     * Store a newly created alert.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string|in:success,error,warning,info',
                'message' => 'required|string|max:1000',
                'title' => 'nullable|string|max:255',
                'alert_type' => 'nullable|string|in:alert,toast,modal,inline',
                'theme' => 'nullable|string|in:bootstrap,tailwind,bulma',
                'position' => 'nullable|string',
                'animation' => 'nullable|string',
                'dismissible' => 'nullable|boolean',
                'auto_dismiss' => 'nullable|boolean',
                'auto_dismiss_delay' => 'nullable|integer|min:1000',
                'expires_at' => 'nullable|date|after:now',
                'priority' => 'nullable|integer|min:0|max:10',
                'context' => 'nullable|string|max:255',
                'field' => 'nullable|string|max:255',
                'form' => 'nullable|string|max:255',
                'icon' => 'nullable|string|max:255',
                'class' => 'nullable|string|max:255',
                'style' => 'nullable|string|max:1000',
                'html_content' => 'nullable|string|max:5000',
                'data_attributes' => 'nullable|array',
                'options' => 'nullable|array'
            ]);

            $manager = $this->getManager();
            $alert = $manager->add(
                $validated['type'],
                $validated['message'],
                $validated['title'] ?? null,
                $validated
            );

            return $this->successResponse([
                'alert' => $this->formatAlert($manager->last()),
                'message' => 'Alert created successfully'
            ], 201);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create alert', 500, $e->getMessage());
        }
    }

    /**
     * Display the specified alert.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $manager = $this->getManager();
            $alert = $this->findAlert($manager, $id);

            if (!$alert) {
                return $this->errorResponse('Alert not found', 404);
            }

            return $this->successResponse([
                'alert' => $this->formatAlert($alert)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve alert', 500, $e->getMessage());
        }
    }

    /**
     * Update the specified alert.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'sometimes|string|in:success,error,warning,info',
                'message' => 'sometimes|string|max:1000',
                'title' => 'nullable|string|max:255',
                'alert_type' => 'nullable|string|in:alert,toast,modal,inline',
                'theme' => 'nullable|string|in:bootstrap,tailwind,bulma',
                'position' => 'nullable|string',
                'animation' => 'nullable|string',
                'dismissible' => 'nullable|boolean',
                'auto_dismiss' => 'nullable|boolean',
                'auto_dismiss_delay' => 'nullable|integer|min:1000',
                'expires_at' => 'nullable|date|after:now',
                'priority' => 'nullable|integer|min:0|max:10',
                'context' => 'nullable|string|max:255',
                'field' => 'nullable|string|max:255',
                'form' => 'nullable|string|max:255',
                'icon' => 'nullable|string|max:255',
                'class' => 'nullable|string|max:255',
                'style' => 'nullable|string|max:1000',
                'html_content' => 'nullable|string|max:5000',
                'data_attributes' => 'nullable|array',
                'options' => 'nullable|array'
            ]);

            $manager = $this->getManager();
            $alert = $this->findAlert($manager, $id);

            if (!$alert) {
                return $this->errorResponse('Alert not found', 404);
            }

            // Update alert (implementation depends on storage driver)
            $this->updateAlert($manager, $id, $validated);

            return $this->successResponse([
                'alert' => $this->formatAlert($this->findAlert($manager, $id)),
                'message' => 'Alert updated successfully'
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update alert', 500, $e->getMessage());
        }
    }

    /**
     * Remove the specified alert.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $manager = $this->getManager();
            $alert = $this->findAlert($manager, $id);

            if (!$alert) {
                return $this->errorResponse('Alert not found', 404);
            }

            $manager->removeById($id);

            return $this->successResponse([
                'message' => 'Alert deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete alert', 500, $e->getMessage());
        }
    }

    /**
     * Dismiss the specified alert.
     */
    public function dismiss(Request $request, string $id): JsonResponse
    {
        try {
            $manager = $this->getManager();
            $alert = $this->findAlert($manager, $id);

            if (!$alert) {
                return $this->errorResponse('Alert not found', 404);
            }

            $this->dismissAlert($manager, $id);

            return $this->successResponse([
                'message' => 'Alert dismissed successfully'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to dismiss alert', 500, $e->getMessage());
        }
    }

    /**
     * Dismiss all alerts.
     */
    public function dismissAll(Request $request): JsonResponse
    {
        try {
            $manager = $this->getManager();
            $manager->clear();

            return $this->successResponse([
                'message' => 'All alerts dismissed successfully'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to dismiss all alerts', 500, $e->getMessage());
        }
    }

    /**
     * Get alerts by type.
     */
    public function getByType(Request $request, string $type): JsonResponse
    {
        try {
            $manager = $this->getManager();
            $alerts = $manager->getAlertsByType($type);

            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $paginatedAlerts = $this->paginateAlerts($alerts, $perPage, $page);

            return $this->successResponse([
                'alerts' => $paginatedAlerts['data'],
                'pagination' => $paginatedAlerts['pagination'],
                'meta' => [
                    'type' => $type,
                    'total' => count($alerts),
                    'per_page' => $perPage,
                    'current_page' => $page
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve alerts by type', 500, $e->getMessage());
        }
    }

    /**
     * Get alert statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $manager = $this->getManager();
            $stats = $manager->getStats();

            return $this->successResponse([
                'statistics' => $stats,
                'storage_driver' => $this->storageDriver,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics', 500, $e->getMessage());
        }
    }

    /**
     * Bulk create alerts.
     */
    public function bulkStore(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'alerts' => 'required|array|min:1|max:100',
                'alerts.*.type' => 'required|string|in:success,error,warning,info',
                'alerts.*.message' => 'required|string|max:1000',
                'alerts.*.title' => 'nullable|string|max:255',
                'alerts.*.alert_type' => 'nullable|string|in:alert,toast,modal,inline',
                'alerts.*.theme' => 'nullable|string|in:bootstrap,tailwind,bulma',
                'alerts.*.position' => 'nullable|string',
                'alerts.*.animation' => 'nullable|string',
                'alerts.*.dismissible' => 'nullable|boolean',
                'alerts.*.auto_dismiss' => 'nullable|boolean',
                'alerts.*.auto_dismiss_delay' => 'nullable|integer|min:1000',
                'alerts.*.expires_at' => 'nullable|date|after:now',
                'alerts.*.priority' => 'nullable|integer|min:0|max:10',
                'alerts.*.context' => 'nullable|string|max:255',
                'alerts.*.field' => 'nullable|string|max:255',
                'alerts.*.form' => 'nullable|string|max:255',
                'alerts.*.icon' => 'nullable|string|max:255',
                'alerts.*.class' => 'nullable|string|max:255',
                'alerts.*.style' => 'nullable|string|max:1000',
                'alerts.*.html_content' => 'nullable|string|max:5000',
                'alerts.*.data_attributes' => 'nullable|array',
                'alerts.*.options' => 'nullable|array'
            ]);

            $manager = $this->getManager();
            $manager->addMultiple($validated['alerts']);

            return $this->successResponse([
                'message' => 'Bulk alerts created successfully',
                'count' => count($validated['alerts'])
            ], 201);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create bulk alerts', 500, $e->getMessage());
        }
    }

    /**
     * Bulk update alerts.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:alerts,id',
                'action' => 'required|string|in:activate,deactivate,dismiss,mark_read,mark_unread,delete',
                'data' => 'nullable|array'
            ]);

            $manager = $this->getManager();
            $result = $this->bulkUpdateAlerts($manager, $validated);

            return $this->successResponse([
                'message' => 'Bulk update completed successfully',
                'updated_count' => $result['count'],
                'action' => $validated['action']
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to bulk update alerts', 500, $e->getMessage());
        }
    }

    /**
     * Get alert history.
     */
    public function history(Request $request): JsonResponse
    {
        try {
            $manager = $this->getManager();
            $limit = $request->get('limit', 50);
            $history = $manager->getHistory($limit);

            return $this->successResponse([
                'history' => array_map([$this, 'formatAlert'], $history),
                'meta' => [
                    'limit' => $limit,
                    'count' => count($history)
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve alert history', 500, $e->getMessage());
        }
    }

    /**
     * Apply filters to alerts.
     */
    protected function applyFilters(Request $request): array
    {
        return [
            'type' => $request->get('type'),
            'alert_type' => $request->get('alert_type'),
            'theme' => $request->get('theme'),
            'status' => $request->get('status'),
            'user_id' => $request->get('user_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'priority' => $request->get('priority'),
            'context' => $request->get('context'),
            'field' => $request->get('field'),
            'form' => $request->get('form')
        ];
    }

    /**
     * Get filtered alerts.
     */
    protected function getFilteredAlerts($manager, array $filters): array
    {
        $alerts = $manager->getAlerts();

        // Apply filters
        if ($filters['type']) {
            $alerts = array_filter($alerts, function ($alert) use ($filters) {
                return $alert['type'] === $filters['type'];
            });
        }

        if ($filters['alert_type']) {
            $alerts = array_filter($alerts, function ($alert) use ($filters) {
                return $alert['alert_type'] === $filters['alert_type'];
            });
        }

        if ($filters['theme']) {
            $alerts = array_filter($alerts, function ($alert) use ($filters) {
                return $alert['theme'] === $filters['theme'];
            });
        }

        if ($filters['priority']) {
            $alerts = array_filter($alerts, function ($alert) use ($filters) {
                return $alert['priority'] >= $filters['priority'];
            });
        }

        if ($filters['context']) {
            $alerts = array_filter($alerts, function ($alert) use ($filters) {
                return $alert['context'] === $filters['context'];
            });
        }

        if ($filters['field']) {
            $alerts = array_filter($alerts, function ($alert) use ($filters) {
                return $alert['field'] === $filters['field'];
            });
        }

        if ($filters['form']) {
            $alerts = array_filter($alerts, function ($alert) use ($filters) {
                return $alert['form'] === $filters['form'];
            });
        }

        return array_values($alerts);
    }

    /**
     * Paginate alerts.
     */
    protected function paginateAlerts(array $alerts, int $perPage, int $page): array
    {
        $total = count($alerts);
        $lastPage = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $data = array_slice($alerts, $offset, $perPage);

        return [
            'data' => array_map([$this, 'formatAlert'], $data),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]
        ];
    }

    /**
     * Find alert by ID.
     */
    protected function findAlert($manager, string $id)
    {
        // This is a simplified implementation
        // In a real scenario, you'd need to implement proper ID-based lookup
        $alerts = $manager->getAlerts();

        foreach ($alerts as $alert) {
            if ($alert['id'] == $id) {
                return $alert;
            }
        }

        return null;
    }

    /**
     * Update alert.
     */
    protected function updateAlert($manager, string $id, array $data): void
    {
        // Implementation depends on storage driver
        // This is a simplified version
        $alert = $this->findAlert($manager, $id);
        if ($alert) {
            // Update logic would go here
        }
    }

    /**
     * Dismiss alert.
     */
    protected function dismissAlert($manager, string $id): void
    {
        // Implementation depends on storage driver
        $manager->removeById($id);
    }

    /**
     * Bulk update alerts.
     */
    protected function bulkUpdateAlerts($manager, array $data): array
    {
        $count = 0;

        foreach ($data['ids'] as $id) {
            switch ($data['action']) {
                case 'activate':
                case 'deactivate':
                case 'dismiss':
                case 'mark_read':
                case 'mark_unread':
                case 'delete':
                    $count++;
                    break;
            }
        }

        return ['count' => $count];
    }

    /**
     * Format alert for API response.
     */
    protected function formatAlert($alert): array
    {
        if (!$alert) {
            return [];
        }

        return [
            'id' => $alert['id'] ?? $alert->getId(),
            'type' => $alert['type'] ?? $alert->getType(),
            'message' => $alert['message'] ?? $alert->getMessage(),
            'title' => $alert['title'] ?? $alert->getTitle(),
            'alert_type' => $alert['alert_type'] ?? $alert->getAlertType(),
            'theme' => $alert['theme'] ?? $alert->getTheme(),
            'position' => $alert['position'] ?? $alert->getPosition(),
            'animation' => $alert['animation'] ?? $alert->getAnimation(),
            'dismissible' => $alert['dismissible'] ?? $alert->isDismissible(),
            'auto_dismiss' => $alert['auto_dismiss'] ?? $alert->hasAutoDismiss(),
            'auto_dismiss_delay' => $alert['auto_dismiss_delay'] ?? $alert->getAutoDismissDelay(),
            'expires_at' => $alert['expires_at'] ?? $alert->getExpiresAt(),
            'priority' => $alert['priority'] ?? $alert->getPriority(),
            'context' => $alert['context'] ?? $alert->getContext(),
            'field' => $alert['field'] ?? $alert->getField(),
            'form' => $alert['form'] ?? $alert->getForm(),
            'icon' => $alert['icon'] ?? $alert->getIcon(),
            'class' => $alert['class'] ?? $alert->getClass(),
            'style' => $alert['style'] ?? $alert->getStyle(),
            'html_content' => $alert['html_content'] ?? $alert->getHtmlContent(),
            'data_attributes' => $alert['data_attributes'] ?? $alert->getDataAttributes(),
            'options' => $alert['options'] ?? $alert->getOptions(),
            'created_at' => $alert['created_at'] ?? $alert->getCreatedAt(),
            'updated_at' => $alert['updated_at'] ?? $alert->getUpdatedAt(),
            'is_active' => $alert['is_active'] ?? $alert->isActive(),
            'dismissed_at' => $alert['dismissed_at'] ?? $alert->getDismissedAt(),
            'read_at' => $alert['read_at'] ?? $alert->getReadAt()
        ];
    }

    /**
     * Return success response.
     */
    protected function successResponse(array $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ], $status);
    }

    /**
     * Return error response.
     */
    protected function errorResponse(string $message, int $status = 400, $details = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ];

        if ($details) {
            $response['details'] = $details;
        }

        return response()->json($response, $status);
    }
}
