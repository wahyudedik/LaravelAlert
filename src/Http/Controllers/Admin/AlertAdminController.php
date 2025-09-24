<?php

namespace Wahyudedik\LaravelAlert\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Wahyudedik\LaravelAlert\Models\DatabaseAlert;
use Wahyudedik\LaravelAlert\Managers\DatabaseAlertManager;

class AlertAdminController extends Controller
{
    protected DatabaseAlertManager $alertManager;

    public function __construct(DatabaseAlertManager $alertManager)
    {
        $this->alertManager = $alertManager;
    }

    /**
     * Display the admin dashboard.
     */
    public function index(Request $request): View
    {
        $filters = [
            'type' => $request->get('type'),
            'alert_type' => $request->get('alert_type'),
            'theme' => $request->get('theme'),
            'status' => $request->get('status'),
            'user_id' => $request->get('user_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $query = DatabaseAlert::query();

        // Apply filters
        if ($filters['type']) {
            $query->ofType($filters['type']);
        }

        if ($filters['alert_type']) {
            $query->ofAlertType($filters['alert_type']);
        }

        if ($filters['theme']) {
            $query->withTheme($filters['theme']);
        }

        if ($filters['user_id']) {
            $query->forUser($filters['user_id']);
        }

        if ($filters['date_from']) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Apply status filter
        if ($filters['status']) {
            switch ($filters['status']) {
                case 'active':
                    $query->active()->notExpired()->notDismissed();
                    break;
                case 'expired':
                    $query->where('expires_at', '<', now());
                    break;
                case 'dismissed':
                    $query->whereNotNull('dismissed_at');
                    break;
                case 'read':
                    $query->whereNotNull('read_at');
                    break;
                case 'unread':
                    $query->whereNull('read_at');
                    break;
            }
        }

        $alerts = $query->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $stats = $this->getStats();

        return view('laravel-alert::admin.index', compact('alerts', 'filters', 'stats'));
    }

    /**
     * Show alert details.
     */
    public function show(int $id): View
    {
        $alert = DatabaseAlert::with('user')->findOrFail($id);

        return view('laravel-alert::admin.show', compact('alert'));
    }

    /**
     * Create a new alert.
     */
    public function create(): View
    {
        return view('laravel-alert::admin.create');
    }

    /**
     * Store a new alert.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:success,error,warning,info',
            'message' => 'required|string|max:1000',
            'title' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
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
            'html_content' => 'nullable|string|max:5000'
        ]);

        $alertData = $request->only([
            'type',
            'message',
            'title',
            'user_id',
            'alert_type',
            'theme',
            'position',
            'animation',
            'dismissible',
            'auto_dismiss',
            'auto_dismiss_delay',
            'expires_at',
            'priority',
            'context',
            'field',
            'form',
            'icon',
            'class',
            'style',
            'html_content'
        ]);

        $alertData['session_id'] = null; // Admin-created alerts don't have session
        $alertData['is_active'] = true;

        $alert = DatabaseAlert::create($alertData);

        return response()->json([
            'success' => true,
            'message' => 'Alert created successfully',
            'alert' => $alert
        ]);
    }

    /**
     * Edit an alert.
     */
    public function edit(int $id): View
    {
        $alert = DatabaseAlert::findOrFail($id);

        return view('laravel-alert::admin.edit', compact('alert'));
    }

    /**
     * Update an alert.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $alert = DatabaseAlert::findOrFail($id);

        $request->validate([
            'type' => 'required|string|in:success,error,warning,info',
            'message' => 'required|string|max:1000',
            'title' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
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
            'html_content' => 'nullable|string|max:5000'
        ]);

        $alertData = $request->only([
            'type',
            'message',
            'title',
            'user_id',
            'alert_type',
            'theme',
            'position',
            'animation',
            'dismissible',
            'auto_dismiss',
            'auto_dismiss_delay',
            'expires_at',
            'priority',
            'context',
            'field',
            'form',
            'icon',
            'class',
            'style',
            'html_content'
        ]);

        $alert->update($alertData);

        return response()->json([
            'success' => true,
            'message' => 'Alert updated successfully',
            'alert' => $alert
        ]);
    }

    /**
     * Delete an alert.
     */
    public function destroy(int $id): JsonResponse
    {
        $alert = DatabaseAlert::findOrFail($id);
        $alert->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alert deleted successfully'
        ]);
    }

    /**
     * Bulk delete alerts.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:alerts,id'
        ]);

        $deleted = DatabaseAlert::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deleted} alerts deleted successfully"
        ]);
    }

    /**
     * Bulk update alerts.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:alerts,id',
            'action' => 'required|string|in:activate,deactivate,dismiss,mark_read,mark_unread,delete'
        ]);

        $ids = $request->ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                $updated = DatabaseAlert::whereIn('id', $ids)->update(['is_active' => true]);
                break;
            case 'deactivate':
                $updated = DatabaseAlert::whereIn('id', $ids)->update(['is_active' => false]);
                break;
            case 'dismiss':
                $updated = DatabaseAlert::whereIn('id', $ids)->update(['dismissed_at' => now()]);
                break;
            case 'mark_read':
                $updated = DatabaseAlert::whereIn('id', $ids)->update(['read_at' => now()]);
                break;
            case 'mark_unread':
                $updated = DatabaseAlert::whereIn('id', $ids)->update(['read_at' => null]);
                break;
            case 'delete':
                $updated = DatabaseAlert::whereIn('id', $ids)->delete();
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action'
                ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => "{$updated} alerts updated successfully"
        ]);
    }

    /**
     * Get alert statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = $this->getStats();

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Export alerts.
     */
    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|string|in:json,csv,xml',
            'filters' => 'nullable|array'
        ]);

        $filters = $request->get('filters', []);
        $format = $request->get('format');

        $query = DatabaseAlert::query();

        // Apply filters
        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['alert_type'])) {
            $query->ofAlertType($filters['alert_type']);
        }

        if (isset($filters['user_id'])) {
            $query->forUser($filters['user_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $alerts = $query->with('user')->get();

        switch ($format) {
            case 'json':
                return response()->json($alerts);
            case 'csv':
                return $this->exportCsv($alerts);
            case 'xml':
                return $this->exportXml($alerts);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    /**
     * Get alert statistics.
     */
    protected function getStats(): array
    {
        $total = DatabaseAlert::count();
        $active = DatabaseAlert::active()->notExpired()->notDismissed()->count();
        $expired = DatabaseAlert::where('expires_at', '<', now())->count();
        $dismissed = DatabaseAlert::whereNotNull('dismissed_at')->count();
        $read = DatabaseAlert::whereNotNull('read_at')->count();

        $byType = DatabaseAlert::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $byAlertType = DatabaseAlert::selectRaw('alert_type, COUNT(*) as count')
            ->groupBy('alert_type')
            ->pluck('count', 'alert_type')
            ->toArray();

        $byTheme = DatabaseAlert::selectRaw('theme, COUNT(*) as count')
            ->groupBy('theme')
            ->pluck('count', 'theme')
            ->toArray();

        $recent = DatabaseAlert::where('created_at', '>=', now()->subDays(7))->count();

        return [
            'total' => $total,
            'active' => $active,
            'expired' => $expired,
            'dismissed' => $dismissed,
            'read' => $read,
            'by_type' => $byType,
            'by_alert_type' => $byAlertType,
            'by_theme' => $byTheme,
            'recent' => $recent
        ];
    }

    /**
     * Export alerts as CSV.
     */
    protected function exportCsv($alerts): JsonResponse
    {
        $csv = "ID,Type,Message,Title,User ID,Alert Type,Theme,Position,Animation,Dismissible,Auto Dismiss,Priority,Context,Field,Form,Icon,Class,Style,HTML Content,Created At,Updated At\n";

        foreach ($alerts as $alert) {
            $csv .= implode(',', [
                $alert->id,
                $alert->type,
                '"' . str_replace('"', '""', $alert->message) . '"',
                '"' . str_replace('"', '""', $alert->title ?? '') . '"',
                $alert->user_id ?? '',
                $alert->alert_type,
                $alert->theme,
                $alert->position,
                $alert->animation,
                $alert->dismissible ? 'Yes' : 'No',
                $alert->auto_dismiss ? 'Yes' : 'No',
                $alert->priority,
                $alert->context ?? '',
                $alert->field ?? '',
                $alert->form ?? '',
                $alert->icon ?? '',
                $alert->class ?? '',
                $alert->style ?? '',
                '"' . str_replace('"', '""', $alert->html_content ?? '') . '"',
                $alert->created_at,
                $alert->updated_at
            ]) . "\n";
        }

        return response()->json([
            'success' => true,
            'data' => $csv,
            'filename' => 'alerts_' . date('Y-m-d_H-i-s') . '.csv'
        ]);
    }

    /**
     * Export alerts as XML.
     */
    protected function exportXml($alerts): JsonResponse
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<alerts>' . "\n";

        foreach ($alerts as $alert) {
            $xml .= '  <alert>' . "\n";
            $xml .= '    <id>' . htmlspecialchars($alert->id) . '</id>' . "\n";
            $xml .= '    <type>' . htmlspecialchars($alert->type) . '</type>' . "\n";
            $xml .= '    <message>' . htmlspecialchars($alert->message) . '</message>' . "\n";
            $xml .= '    <title>' . htmlspecialchars($alert->title ?? '') . '</title>' . "\n";
            $xml .= '    <user_id>' . htmlspecialchars($alert->user_id ?? '') . '</user_id>' . "\n";
            $xml .= '    <alert_type>' . htmlspecialchars($alert->alert_type) . '</alert_type>' . "\n";
            $xml .= '    <theme>' . htmlspecialchars($alert->theme) . '</theme>' . "\n";
            $xml .= '    <position>' . htmlspecialchars($alert->position) . '</position>' . "\n";
            $xml .= '    <animation>' . htmlspecialchars($alert->animation) . '</animation>' . "\n";
            $xml .= '    <dismissible>' . ($alert->dismissible ? 'true' : 'false') . '</dismissible>' . "\n";
            $xml .= '    <auto_dismiss>' . ($alert->auto_dismiss ? 'true' : 'false') . '</auto_dismiss>' . "\n";
            $xml .= '    <priority>' . htmlspecialchars($alert->priority) . '</priority>' . "\n";
            $xml .= '    <context>' . htmlspecialchars($alert->context ?? '') . '</context>' . "\n";
            $xml .= '    <field>' . htmlspecialchars($alert->field ?? '') . '</field>' . "\n";
            $xml .= '    <form>' . htmlspecialchars($alert->form ?? '') . '</form>' . "\n";
            $xml .= '    <icon>' . htmlspecialchars($alert->icon ?? '') . '</icon>' . "\n";
            $xml .= '    <class>' . htmlspecialchars($alert->class ?? '') . '</class>' . "\n";
            $xml .= '    <style>' . htmlspecialchars($alert->style ?? '') . '</style>' . "\n";
            $xml .= '    <html_content><![CDATA[' . ($alert->html_content ?? '') . ']]></html_content>' . "\n";
            $xml .= '    <created_at>' . htmlspecialchars($alert->created_at) . '</created_at>' . "\n";
            $xml .= '    <updated_at>' . htmlspecialchars($alert->updated_at) . '</updated_at>' . "\n";
            $xml .= '  </alert>' . "\n";
        }

        $xml .= '</alerts>' . "\n";

        return response()->json([
            'success' => true,
            'data' => $xml,
            'filename' => 'alerts_' . date('Y-m-d_H-i-s') . '.xml'
        ]);
    }
}
