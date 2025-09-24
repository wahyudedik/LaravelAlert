<?php

namespace Wahyudedik\LaravelAlert\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Wahyudedik\LaravelAlert\Facades\Alert;

class AlertController
{
    /**
     * Get all alerts.
     */
    public function index(): JsonResponse
    {
        $alerts = Alert::getAlerts();

        return response()->json([
            'success' => true,
            'alerts' => $alerts,
            'count' => count($alerts),
            'expired' => Alert::getExpiredAlerts(),
            'auto_dismiss' => Alert::getAutoDismissAlerts(),
        ]);
    }

    /**
     * Dismiss a specific alert.
     */
    public function dismiss(Request $request): JsonResponse
    {
        $request->validate([
            'alert_id' => 'required|string'
        ]);

        $alertId = $request->input('alert_id');
        $alerts = Alert::getAlerts();

        $alert = collect($alerts)->firstWhere('id', $alertId);

        if (!$alert) {
            return response()->json([
                'success' => false,
                'message' => 'Alert not found'
            ], 404);
        }

        Alert::removeById($alertId);

        return response()->json([
            'success' => true,
            'message' => 'Alert dismissed successfully'
        ]);
    }

    /**
     * Dismiss all alerts.
     */
    public function dismissAll(): JsonResponse
    {
        $count = Alert::count();
        Alert::clear();

        return response()->json([
            'success' => true,
            'message' => "Dismissed {$count} alerts"
        ]);
    }

    /**
     * Clear all alerts.
     */
    public function clear(): JsonResponse
    {
        $count = Alert::count();
        Alert::clear();

        return response()->json([
            'success' => true,
            'message' => "Cleared {$count} alerts"
        ]);
    }

    /**
     * Get alerts statistics.
     */
    public function stats(): JsonResponse
    {
        $alerts = Alert::getAlerts();
        $expired = Alert::getExpiredAlerts();
        $autoDismiss = Alert::getAutoDismissAlerts();

        $stats = [
            'total' => count($alerts),
            'expired' => count($expired),
            'auto_dismiss' => count($autoDismiss),
            'by_type' => [
                'success' => count(Alert::getAlertsByType('success')),
                'error' => count(Alert::getAlertsByType('error')),
                'warning' => count(Alert::getAlertsByType('warning')),
                'info' => count(Alert::getAlertsByType('info')),
            ]
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Clean up expired alerts.
     */
    public function cleanup(): JsonResponse
    {
        $expiredCount = count(Alert::getExpiredAlerts());
        Alert::cleanupExpired();

        return response()->json([
            'success' => true,
            'message' => "Cleaned up {$expiredCount} expired alerts"
        ]);
    }
}
