<?php

namespace Wahyudedik\LaravelAlert\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Wahyudedik\LaravelAlert\Managers\AlertManager;
use Wahyudedik\LaravelAlert\Managers\ToastAlertManager;
use Wahyudedik\LaravelAlert\Managers\ModalAlertManager;
use Wahyudedik\LaravelAlert\Managers\InlineAlertManager;

class AjaxAlertController extends Controller
{
    protected AlertManager $alertManager;
    protected ToastAlertManager $toastManager;
    protected ModalAlertManager $modalManager;
    protected InlineAlertManager $inlineManager;

    public function __construct(
        AlertManager $alertManager,
        ToastAlertManager $toastManager,
        ModalAlertManager $modalManager,
        InlineAlertManager $inlineManager
    ) {
        $this->alertManager = $alertManager;
        $this->toastManager = $toastManager;
        $this->modalManager = $modalManager;
        $this->inlineManager = $inlineManager;
    }

    /**
     * Get all alerts
     */
    public function index(Request $request): JsonResponse
    {
        $type = $request->get('type', 'all');
        $alerts = [];

        switch ($type) {
            case 'toast':
                $alerts = $this->toastManager->getAlerts();
                break;
            case 'modal':
                $alerts = $this->modalManager->getAlerts();
                break;
            case 'inline':
                $alerts = $this->inlineManager->getAlerts();
                break;
            default:
                $alerts = $this->alertManager->getAlerts();
        }

        return response()->json([
            'success' => true,
            'alerts' => $this->formatAlerts($alerts),
            'count' => count($alerts)
        ]);
    }

    /**
     * Create a new alert
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:success,error,warning,info',
            'message' => 'required|string',
            'title' => 'nullable|string',
            'alert_type' => 'nullable|string|in:alert,toast,modal,inline',
            'options' => 'nullable|array'
        ]);

        $type = $request->input('type');
        $message = $request->input('message');
        $title = $request->input('title');
        $alertType = $request->input('alert_type', 'alert');
        $options = $request->input('options', []);

        $alertId = null;

        switch ($alertType) {
            case 'toast':
                $this->toastManager->add($type, $message, $title, $options);
                $alertId = $this->toastManager->last()?->getId();
                break;
            case 'modal':
                $this->modalManager->add($type, $message, $title, $options);
                $alertId = $this->modalManager->last()?->getId();
                break;
            case 'inline':
                $this->inlineManager->add($type, $message, $title, $options);
                $alertId = $this->inlineManager->last()?->getId();
                break;
            default:
                $this->alertManager->add($type, $message, $title, $options);
                $alertId = $this->alertManager->last()?->getId();
        }

        return response()->json([
            'success' => true,
            'alert_id' => $alertId,
            'message' => 'Alert created successfully'
        ]);
    }

    /**
     * Dismiss an alert
     */
    public function dismiss(Request $request): JsonResponse
    {
        $request->validate([
            'alert_id' => 'required|string',
            'alert_type' => 'nullable|string|in:alert,toast,modal,inline'
        ]);

        $alertId = $request->input('alert_id');
        $alertType = $request->input('alert_type', 'alert');

        $success = false;

        switch ($alertType) {
            case 'toast':
                $success = $this->toastManager->removeById($alertId);
                break;
            case 'modal':
                $success = $this->modalManager->removeById($alertId);
                break;
            case 'inline':
                $success = $this->inlineManager->removeById($alertId);
                break;
            default:
                $success = $this->alertManager->removeById($alertId);
        }

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Alert dismissed successfully' : 'Alert not found'
        ]);
    }

    /**
     * Dismiss all alerts
     */
    public function dismissAll(Request $request): JsonResponse
    {
        $alertType = $request->input('alert_type', 'alert');

        switch ($alertType) {
            case 'toast':
                $this->toastManager->clear();
                break;
            case 'modal':
                $this->modalManager->clear();
                break;
            case 'inline':
                $this->inlineManager->clear();
                break;
            default:
                $this->alertManager->clear();
        }

        return response()->json([
            'success' => true,
            'message' => 'All alerts dismissed successfully'
        ]);
    }

    /**
     * Clear alerts by type
     */
    public function clearByType(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:success,error,warning,info',
            'alert_type' => 'nullable|string|in:alert,toast,modal,inline'
        ]);

        $type = $request->input('type');
        $alertType = $request->input('alert_type', 'alert');

        switch ($alertType) {
            case 'toast':
                $this->toastManager->clearByType($type);
                break;
            case 'modal':
                $this->modalManager->clearByType($type);
                break;
            case 'inline':
                $this->inlineManager->clearByType($type);
                break;
            default:
                $this->alertManager->clearByType($type);
        }

        return response()->json([
            'success' => true,
            'message' => "All {$type} alerts cleared successfully"
        ]);
    }

    /**
     * Get alerts by type
     */
    public function getByType(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:success,error,warning,info',
            'alert_type' => 'nullable|string|in:alert,toast,modal,inline'
        ]);

        $type = $request->input('type');
        $alertType = $request->input('alert_type', 'alert');

        $alerts = [];

        switch ($alertType) {
            case 'toast':
                $alerts = $this->toastManager->getAlertsByType($type);
                break;
            case 'modal':
                $alerts = $this->modalManager->getAlertsByType($type);
                break;
            case 'inline':
                $alerts = $this->inlineManager->getAlertsByType($type);
                break;
            default:
                $alerts = $this->alertManager->getAlertsByType($type);
        }

        return response()->json([
            'success' => true,
            'alerts' => $this->formatAlerts($alerts),
            'count' => count($alerts)
        ]);
    }

    /**
     * Get alert statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $alertType = $request->input('alert_type', 'alert');

        $stats = [];

        switch ($alertType) {
            case 'toast':
                $stats = [
                    'total' => $this->toastManager->count(),
                    'success' => count($this->toastManager->getAlertsByType('success')),
                    'error' => count($this->toastManager->getAlertsByType('error')),
                    'warning' => count($this->toastManager->getAlertsByType('warning')),
                    'info' => count($this->toastManager->getAlertsByType('info'))
                ];
                break;
            case 'modal':
                $stats = [
                    'total' => $this->modalManager->count(),
                    'success' => count($this->modalManager->getAlertsByType('success')),
                    'error' => count($this->modalManager->getAlertsByType('error')),
                    'warning' => count($this->modalManager->getAlertsByType('warning')),
                    'info' => count($this->modalManager->getAlertsByType('info'))
                ];
                break;
            case 'inline':
                $stats = [
                    'total' => $this->inlineManager->count(),
                    'success' => count($this->inlineManager->getAlertsByType('success')),
                    'error' => count($this->inlineManager->getAlertsByType('error')),
                    'warning' => count($this->inlineManager->getAlertsByType('warning')),
                    'info' => count($this->inlineManager->getAlertsByType('info'))
                ];
                break;
            default:
                $stats = [
                    'total' => $this->alertManager->count(),
                    'success' => count($this->alertManager->getAlertsByType('success')),
                    'error' => count($this->alertManager->getAlertsByType('error')),
                    'warning' => count($this->alertManager->getAlertsByType('warning')),
                    'info' => count($this->alertManager->getAlertsByType('info'))
                ];
        }

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Create multiple alerts
     */
    public function createMultiple(Request $request): JsonResponse
    {
        $request->validate([
            'alerts' => 'required|array|min:1',
            'alerts.*.type' => 'required|string|in:success,error,warning,info',
            'alerts.*.message' => 'required|string',
            'alerts.*.title' => 'nullable|string',
            'alerts.*.options' => 'nullable|array',
            'alert_type' => 'nullable|string|in:alert,toast,modal,inline'
        ]);

        $alerts = $request->input('alerts');
        $alertType = $request->input('alert_type', 'alert');
        $createdIds = [];

        switch ($alertType) {
            case 'toast':
                $this->toastManager->addMultiple($alerts);
                $createdIds = array_map(fn($alert) => $alert->getId(), $this->toastManager->getAlerts());
                break;
            case 'modal':
                $this->modalManager->addMultiple($alerts);
                $createdIds = array_map(fn($alert) => $alert->getId(), $this->modalManager->getAlerts());
                break;
            case 'inline':
                $this->inlineManager->addMultiple($alerts);
                $createdIds = array_map(fn($alert) => $alert->getId(), $this->inlineManager->getAlerts());
                break;
            default:
                $this->alertManager->addMultiple($alerts);
                $createdIds = array_map(fn($alert) => $alert->getId(), $this->alertManager->getAlerts());
        }

        return response()->json([
            'success' => true,
            'alert_ids' => $createdIds,
            'message' => 'Multiple alerts created successfully'
        ]);
    }

    /**
     * Format alerts for JSON response
     */
    protected function formatAlerts(array $alerts): array
    {
        return array_map(function ($alert) {
            return [
                'id' => $alert->getId(),
                'type' => $alert->getType(),
                'message' => $alert->getMessage(),
                'title' => $alert->getTitle(),
                'dismissible' => $alert->isDismissible(),
                'auto_dismiss' => $alert->shouldAutoDismiss(),
                'auto_dismiss_delay' => $alert->getAutoDismissDelay(),
                'expires_at' => $alert->getExpiresAt(),
                'animation' => $alert->getAnimation(),
                'position' => $alert->getPosition(),
                'theme' => $alert->getTheme(),
                'class' => $alert->getClass(),
                'style' => $alert->getStyle(),
                'icon' => $alert->getIcon(),
                'html_content' => $alert->getHtmlContent(),
                'data_attributes' => $alert->getDataAttributes(),
                'options' => $alert->getOptions(),
                'created_at' => $alert->getCreatedAt()
            ];
        }, $alerts);
    }
}
