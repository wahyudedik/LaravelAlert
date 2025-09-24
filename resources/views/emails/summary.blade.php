<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Summary - {{ $summary['date'] ?? now()->format('Y-m-d') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .summary-header {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-title {
            font-size: 24px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
        }

        .summary-date {
            font-size: 16px;
            color: #666;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .stat-card {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
        }

        .stat-success {
            color: #28a745;
        }

        .stat-error {
            color: #dc3545;
        }

        .stat-warning {
            color: #ffc107;
        }

        .stat-info {
            color: #17a2b8;
        }

        .chart-container {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .chart-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #495057;
        }

        .chart-placeholder {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 4px;
            padding: 40px;
            text-align: center;
            color: #666;
        }

        .alerts-list {
            margin: 20px 0;
        }

        .alert-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            background-color: #fff;
        }

        .alert-header {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .alert-message {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .alert-meta {
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 8px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="summary-header">
        <div class="summary-title">Alert Summary</div>
        <div class="summary-date">{{ $summary['date'] ?? now()->format('Y-m-d') }}</div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number stat-success">{{ $summary['total_alerts'] ?? 0 }}</div>
            <div class="stat-label">Total Alerts</div>
        </div>

        <div class="stat-card">
            <div class="stat-number stat-success">{{ $summary['success_count'] ?? 0 }}</div>
            <div class="stat-label">Success</div>
        </div>

        <div class="stat-card">
            <div class="stat-number stat-error">{{ $summary['error_count'] ?? 0 }}</div>
            <div class="stat-label">Errors</div>
        </div>

        <div class="stat-card">
            <div class="stat-number stat-warning">{{ $summary['warning_count'] ?? 0 }}</div>
            <div class="stat-label">Warnings</div>
        </div>

        <div class="stat-card">
            <div class="stat-number stat-info">{{ $summary['info_count'] ?? 0 }}</div>
            <div class="stat-label">Info</div>
        </div>

        <div class="stat-card">
            <div class="stat-number">{{ $summary['dismissed_count'] ?? 0 }}</div>
            <div class="stat-label">Dismissed</div>
        </div>
    </div>

    @if (isset($summary['chart_data']) && $summary['chart_data'])
        <div class="chart-container">
            <div class="chart-title">Alert Trends</div>
            <div class="chart-placeholder">
                Chart data available: {{ json_encode($summary['chart_data']) }}
            </div>
        </div>
    @endif

    @if (isset($summary['recent_alerts']) && count($summary['recent_alerts']) > 0)
        <div class="alerts-list">
            <h3>Recent Alerts</h3>
            @foreach ($summary['recent_alerts'] as $alert)
                <div class="alert-item">
                    <div class="alert-header">
                        {{ $alert['title'] ?? ucfirst($alert['type'] ?? 'Alert') }}
                    </div>

                    <div class="alert-message">
                        {{ $alert['message'] }}
                    </div>

                    <div class="alert-meta">
                        <strong>Type:</strong> {{ ucfirst($alert['type'] ?? 'info') }} |
                        <strong>Priority:</strong> {{ $alert['priority'] ?? 'Normal' }} |
                        <strong>Time:</strong>
                        {{ $alert['created_at'] ? \Carbon\Carbon::parse($alert['created_at'])->format('H:i:s') : now()->format('H:i:s') }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if (isset($summary['top_contexts']) && count($summary['top_contexts']) > 0)
        <div class="chart-container">
            <div class="chart-title">Top Contexts</div>
            <ul>
                @foreach ($summary['top_contexts'] as $context => $count)
                    <li><strong>{{ $context }}:</strong> {{ $count }} alerts</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="footer">
        <p>This summary was generated by Laravel Alert system.</p>
        <p>For more detailed information, please check your admin panel.</p>
        <p>If you no longer wish to receive these notifications, please contact your system administrator.</p>
    </div>
</body>

</html>
