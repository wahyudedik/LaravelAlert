<?php

namespace Wahyudedik\LaravelAlert\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class AlertApiResponse
{
    protected array $meta = [];
    protected array $links = [];
    protected array $errors = [];

    /**
     * Create a success response.
     */
    public static function success(array $data = [], string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'status' => $status
            ]
        ], $status);
    }

    /**
     * Create an error response.
     */
    public static function error(string $message = 'Error', int $status = 400, array $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'status' => $status
            ]
        ], $status);
    }

    /**
     * Create a validation error response.
     */
    public static function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return self::error($message, 422, $errors);
    }

    /**
     * Create a not found response.
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, 404);
    }

    /**
     * Create an unauthorized response.
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, 401);
    }

    /**
     * Create a forbidden response.
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::error($message, 403);
    }

    /**
     * Create a server error response.
     */
    public static function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return self::error($message, 500);
    }

    /**
     * Create a paginated response.
     */
    public static function paginated(array $data, array $pagination, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => $pagination,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'total' => $pagination['total'] ?? 0,
                'per_page' => $pagination['per_page'] ?? 15,
                'current_page' => $pagination['current_page'] ?? 1,
                'last_page' => $pagination['last_page'] ?? 1
            ]
        ]);
    }

    /**
     * Create a collection response.
     */
    public static function collection(Collection $collection, string $message = 'Success'): JsonResponse
    {
        return self::success([
            'items' => $collection->toArray(),
            'count' => $collection->count()
        ], $message);
    }

    /**
     * Create a single resource response.
     */
    public static function resource($resource, string $message = 'Success'): JsonResponse
    {
        return self::success([
            'item' => $resource
        ], $message);
    }

    /**
     * Create a created response.
     */
    public static function created(array $data = [], string $message = 'Resource created successfully'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * Create an updated response.
     */
    public static function updated(array $data = [], string $message = 'Resource updated successfully'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Create a deleted response.
     */
    public static function deleted(string $message = 'Resource deleted successfully'): JsonResponse
    {
        return self::success([], $message, 200);
    }

    /**
     * Create a no content response.
     */
    public static function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Create a custom response.
     */
    public static function custom(array $data, int $status = 200): JsonResponse
    {
        return response()->json($data, $status);
    }

    /**
     * Format alert data for API response.
     */
    public static function formatAlert($alert): array
    {
        if (!$alert) {
            return [];
        }

        // Handle array format
        if (is_array($alert)) {
            return [
                'id' => $alert['id'] ?? null,
                'type' => $alert['type'] ?? null,
                'message' => $alert['message'] ?? null,
                'title' => $alert['title'] ?? null,
                'alert_type' => $alert['alert_type'] ?? 'alert',
                'theme' => $alert['theme'] ?? 'bootstrap',
                'position' => $alert['position'] ?? 'top-right',
                'animation' => $alert['animation'] ?? 'fade',
                'dismissible' => $alert['dismissible'] ?? true,
                'auto_dismiss' => $alert['auto_dismiss'] ?? false,
                'auto_dismiss_delay' => $alert['auto_dismiss_delay'] ?? null,
                'expires_at' => $alert['expires_at'] ?? null,
                'priority' => $alert['priority'] ?? 0,
                'context' => $alert['context'] ?? null,
                'field' => $alert['field'] ?? null,
                'form' => $alert['form'] ?? null,
                'icon' => $alert['icon'] ?? null,
                'class' => $alert['class'] ?? null,
                'style' => $alert['style'] ?? null,
                'html_content' => $alert['html_content'] ?? null,
                'data_attributes' => $alert['data_attributes'] ?? null,
                'options' => $alert['options'] ?? null,
                'created_at' => $alert['created_at'] ?? null,
                'updated_at' => $alert['updated_at'] ?? null,
                'is_active' => $alert['is_active'] ?? true,
                'dismissed_at' => $alert['dismissed_at'] ?? null,
                'read_at' => $alert['read_at'] ?? null
            ];
        }

        // Handle object format
        return [
            'id' => $alert->getId(),
            'type' => $alert->getType(),
            'message' => $alert->getMessage(),
            'title' => $alert->getTitle(),
            'alert_type' => $alert->getAlertType(),
            'theme' => $alert->getTheme(),
            'position' => $alert->getPosition(),
            'animation' => $alert->getAnimation(),
            'dismissible' => $alert->isDismissible(),
            'auto_dismiss' => $alert->hasAutoDismiss(),
            'auto_dismiss_delay' => $alert->getAutoDismissDelay(),
            'expires_at' => $alert->getExpiresAt(),
            'priority' => $alert->getPriority(),
            'context' => $alert->getContext(),
            'field' => $alert->getField(),
            'form' => $alert->getForm(),
            'icon' => $alert->getIcon(),
            'class' => $alert->getClass(),
            'style' => $alert->getStyle(),
            'html_content' => $alert->getHtmlContent(),
            'data_attributes' => $alert->getDataAttributes(),
            'options' => $alert->getOptions(),
            'created_at' => $alert->getCreatedAt(),
            'updated_at' => $alert->getUpdatedAt(),
            'is_active' => $alert->isActive(),
            'dismissed_at' => $alert->getDismissedAt(),
            'read_at' => $alert->getReadAt()
        ];
    }

    /**
     * Format multiple alerts for API response.
     */
    public static function formatAlerts(array $alerts): array
    {
        return array_map([self::class, 'formatAlert'], $alerts);
    }

    /**
     * Create pagination metadata.
     */
    public static function createPaginationMeta(int $total, int $perPage, int $currentPage): array
    {
        $lastPage = ceil($total / $perPage);
        $from = ($currentPage - 1) * $perPage + 1;
        $to = min($from + $perPage - 1, $total);

        return [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'from' => $from,
            'to' => $to,
            'has_more_pages' => $currentPage < $lastPage
        ];
    }

    /**
     * Create links for pagination.
     */
    public static function createPaginationLinks(int $currentPage, int $lastPage, string $baseUrl): array
    {
        $links = [
            'first' => $baseUrl . '?page=1',
            'last' => $baseUrl . '?page=' . $lastPage
        ];

        if ($currentPage > 1) {
            $links['prev'] = $baseUrl . '?page=' . ($currentPage - 1);
        }

        if ($currentPage < $lastPage) {
            $links['next'] = $baseUrl . '?page=' . ($currentPage + 1);
        }

        return $links;
    }

    /**
     * Create API metadata.
     */
    public static function createMeta(array $additional = []): array
    {
        return array_merge([
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0',
            'api_version' => 'v1'
        ], $additional);
    }

    /**
     * Create error details.
     */
    public static function createErrorDetails(string $field, string $message, string $code = null): array
    {
        $error = [
            'field' => $field,
            'message' => $message
        ];

        if ($code) {
            $error['code'] = $code;
        }

        return $error;
    }

    /**
     * Create validation error details.
     */
    public static function createValidationErrors(array $errors): array
    {
        $formattedErrors = [];

        foreach ($errors as $field => $messages) {
            if (is_array($messages)) {
                foreach ($messages as $message) {
                    $formattedErrors[] = self::createErrorDetails($field, $message, 'validation');
                }
            } else {
                $formattedErrors[] = self::createErrorDetails($field, $messages, 'validation');
            }
        }

        return $formattedErrors;
    }

    /**
     * Create success message.
     */
    public static function createSuccessMessage(string $action, string $resource = 'Alert'): string
    {
        $messages = [
            'created' => "{$resource} created successfully",
            'updated' => "{$resource} updated successfully",
            'deleted' => "{$resource} deleted successfully",
            'dismissed' => "{$resource} dismissed successfully",
            'cleared' => "All {$resource}s cleared successfully"
        ];

        return $messages[$action] ?? "{$resource} {$action} successfully";
    }

    /**
     * Create error message.
     */
    public static function createErrorMessage(string $action, string $resource = 'Alert'): string
    {
        $messages = [
            'create' => "Failed to create {$resource}",
            'update' => "Failed to update {$resource}",
            'delete' => "Failed to delete {$resource}",
            'dismiss' => "Failed to dismiss {$resource}",
            'clear' => "Failed to clear {$resource}s",
            'retrieve' => "Failed to retrieve {$resource}",
            'not_found' => "{$resource} not found",
            'unauthorized' => "Unauthorized to access {$resource}",
            'forbidden' => "Forbidden to access {$resource}"
        ];

        return $messages[$action] ?? "Failed to {$action} {$resource}";
    }

    /**
     * Create API response with custom structure.
     */
    public static function customStructure(array $data, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'payload' => $data,
            'meta' => self::createMeta(),
            'links' => []
        ], $status);
    }

    /**
     * Create API response with included resources.
     */
    public static function withIncluded(array $data, array $included, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'included' => $included,
            'meta' => self::createMeta()
        ]);
    }

    /**
     * Create API response with relationships.
     */
    public static function withRelationships(array $data, array $relationships, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'relationships' => $relationships,
            'meta' => self::createMeta()
        ]);
    }
}
