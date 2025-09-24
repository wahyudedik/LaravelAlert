<?php

namespace Wahyudedik\LaravelAlert\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Wahyudedik\LaravelAlert\Managers\AlertManager;
use Wahyudedik\LaravelAlert\Managers\ToastAlertManager;
use Wahyudedik\LaravelAlert\Managers\ModalAlertManager;
use Wahyudedik\LaravelAlert\Managers\InlineAlertManager;

class WebSocketAlertController extends Controller
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
     * Handle WebSocket connection
     */
    public function handleConnection(Request $request): JsonResponse
    {
        // This would typically be handled by a WebSocket server
        // For now, we'll return connection info
        return response()->json([
            'success' => true,
            'message' => 'WebSocket connection established',
            'connection_id' => $this->generateConnectionId()
        ]);
    }

    /**
     * Broadcast alert to all connected clients
     */
    public function broadcast(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:success,error,warning,info',
            'message' => 'required|string',
            'title' => 'nullable|string',
            'alert_type' => 'nullable|string|in:alert,toast,modal,inline',
            'options' => 'nullable|array',
            'channels' => 'nullable|array'
        ]);

        $type = $request->input('type');
        $message = $request->input('message');
        $title = $request->input('title');
        $alertType = $request->input('alert_type', 'alert');
        $options = $request->input('options', []);
        $channels = $request->input('channels', ['all']);

        // Create the alert
        $alertId = $this->createAlert($type, $message, $title, $alertType, $options);

        // Broadcast to WebSocket clients
        $this->broadcastToClients([
            'type' => 'alert',
            'alert' => [
                'id' => $alertId,
                'type' => $type,
                'message' => $message,
                'title' => $title,
                'alert_type' => $alertType,
                'options' => $options
            ],
            'channels' => $channels
        ]);

        return response()->json([
            'success' => true,
            'alert_id' => $alertId,
            'message' => 'Alert broadcasted successfully'
        ]);
    }

    /**
     * Subscribe to alert channels
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'channels' => 'required|array|min:1',
            'channels.*' => 'string'
        ]);

        $channels = $request->input('channels');
        $connectionId = $request->input('connection_id');

        // In a real implementation, you would store the subscription
        // in Redis or another cache system
        $this->storeSubscription($connectionId, $channels);

        return response()->json([
            'success' => true,
            'message' => 'Subscribed to channels successfully',
            'channels' => $channels
        ]);
    }

    /**
     * Unsubscribe from alert channels
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'channels' => 'nullable|array',
            'channels.*' => 'string'
        ]);

        $channels = $request->input('channels', []);
        $connectionId = $request->input('connection_id');

        if (empty($channels)) {
            // Unsubscribe from all channels
            $this->removeSubscription($connectionId);
        } else {
            // Unsubscribe from specific channels
            $this->removeChannelsFromSubscription($connectionId, $channels);
        }

        return response()->json([
            'success' => true,
            'message' => 'Unsubscribed from channels successfully'
        ]);
    }

    /**
     * Get active connections
     */
    public function getConnections(Request $request): JsonResponse
    {
        $connections = $this->getActiveConnections();

        return response()->json([
            'success' => true,
            'connections' => $connections,
            'count' => count($connections)
        ]);
    }

    /**
     * Send alert to specific connection
     */
    public function sendToConnection(Request $request): JsonResponse
    {
        $request->validate([
            'connection_id' => 'required|string',
            'type' => 'required|string|in:success,error,warning,info',
            'message' => 'required|string',
            'title' => 'nullable|string',
            'alert_type' => 'nullable|string|in:alert,toast,modal,inline',
            'options' => 'nullable|array'
        ]);

        $connectionId = $request->input('connection_id');
        $type = $request->input('type');
        $message = $request->input('message');
        $title = $request->input('title');
        $alertType = $request->input('alert_type', 'alert');
        $options = $request->input('options', []);

        // Create the alert
        $alertId = $this->createAlert($type, $message, $title, $alertType, $options);

        // Send to specific connection
        $this->sendToSpecificConnection($connectionId, [
            'type' => 'alert',
            'alert' => [
                'id' => $alertId,
                'type' => $type,
                'message' => $message,
                'title' => $title,
                'alert_type' => $alertType,
                'options' => $options
            ]
        ]);

        return response()->json([
            'success' => true,
            'alert_id' => $alertId,
            'message' => 'Alert sent to connection successfully'
        ]);
    }

    /**
     * Create an alert
     */
    protected function createAlert(string $type, string $message, ?string $title, string $alertType, array $options): string
    {
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

        return $alertId;
    }

    /**
     * Broadcast to all connected clients
     */
    protected function broadcastToClients(array $data): void
    {
        // In a real implementation, you would use a WebSocket server
        // like Pusher, Socket.IO, or a custom WebSocket server
        // For now, we'll simulate the broadcast

        $this->logBroadcast($data);
    }

    /**
     * Send to specific connection
     */
    protected function sendToSpecificConnection(string $connectionId, array $data): void
    {
        // In a real implementation, you would send the data
        // to the specific WebSocket connection

        $this->logBroadcast($data, $connectionId);
    }

    /**
     * Store subscription
     */
    protected function storeSubscription(string $connectionId, array $channels): void
    {
        // In a real implementation, you would store this in Redis
        // or another cache system

        cache()->put("ws_subscription_{$connectionId}", $channels, 3600);
    }

    /**
     * Remove subscription
     */
    protected function removeSubscription(string $connectionId): void
    {
        cache()->forget("ws_subscription_{$connectionId}");
    }

    /**
     * Remove channels from subscription
     */
    protected function removeChannelsFromSubscription(string $connectionId, array $channels): void
    {
        $currentChannels = cache()->get("ws_subscription_{$connectionId}", []);
        $updatedChannels = array_diff($currentChannels, $channels);

        if (empty($updatedChannels)) {
            cache()->forget("ws_subscription_{$connectionId}");
        } else {
            cache()->put("ws_subscription_{$connectionId}", $updatedChannels, 3600);
        }
    }

    /**
     * Get active connections
     */
    protected function getActiveConnections(): array
    {
        // In a real implementation, you would get this from
        // your WebSocket server or connection manager

        return [
            'total' => 0,
            'connections' => []
        ];
    }

    /**
     * Generate connection ID
     */
    protected function generateConnectionId(): string
    {
        return 'ws_' . time() . '_' . bin2hex(random_bytes(8));
    }

    /**
     * Log broadcast (for debugging)
     */
    protected function logBroadcast(array $data, ?string $connectionId = null): void
    {
        $logMessage = 'WebSocket Broadcast: ' . json_encode($data);
        if ($connectionId) {
            $logMessage .= " (to connection: {$connectionId})";
        }

        \Log::info($logMessage);
    }
}
