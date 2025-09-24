@extends('layouts.app')

@section('title', 'Alert Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Alert Management</h3>
                        <div class="card-tools">
                            <a href="{{ route('laravel-alert.admin.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Alert
                            </a>
                            <button type="button" class="btn btn-success" onclick="exportAlerts('json')">
                                <i class="fas fa-download"></i> Export JSON
                            </button>
                            <button type="button" class="btn btn-info" onclick="exportAlerts('csv')">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <select name="type" class="form-control">
                                        <option value="">All Types</option>
                                        <option value="success" {{ request('type') == 'success' ? 'selected' : '' }}>Success
                                        </option>
                                        <option value="error" {{ request('type') == 'error' ? 'selected' : '' }}>Error
                                        </option>
                                        <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>Warning
                                        </option>
                                        <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Info
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="alert_type" class="form-control">
                                        <option value="">All Alert Types</option>
                                        <option value="alert" {{ request('alert_type') == 'alert' ? 'selected' : '' }}>
                                            Alert</option>
                                        <option value="toast" {{ request('alert_type') == 'toast' ? 'selected' : '' }}>
                                            Toast</option>
                                        <option value="modal" {{ request('alert_type') == 'modal' ? 'selected' : '' }}>
                                            Modal</option>
                                        <option value="inline" {{ request('alert_type') == 'inline' ? 'selected' : '' }}>
                                            Inline</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="theme" class="form-control">
                                        <option value="">All Themes</option>
                                        <option value="bootstrap" {{ request('theme') == 'bootstrap' ? 'selected' : '' }}>
                                            Bootstrap</option>
                                        <option value="tailwind" {{ request('theme') == 'tailwind' ? 'selected' : '' }}>
                                            Tailwind</option>
                                        <option value="bulma" {{ request('theme') == 'bulma' ? 'selected' : '' }}>Bulma
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>
                                            Expired</option>
                                        <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>
                                            Dismissed</option>
                                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read
                                        </option>
                                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_from" class="form-control"
                                        value="{{ request('date_from') }}" placeholder="From Date">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_to" class="form-control"
                                        value="{{ request('date_to') }}" placeholder="To Date">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <input type="number" name="user_id" class="form-control"
                                        value="{{ request('user_id') }}" placeholder="User ID">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('laravel-alert.admin.index') }}" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </form>

                        <!-- Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-bell"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total</span>
                                        <span class="info-box-number">{{ $stats['total'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Active</span>
                                        <span class="info-box-number">{{ $stats['active'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Expired</span>
                                        <span class="info-box-number">{{ $stats['expired'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Dismissed</span>
                                        <span class="info-box-number">{{ $stats['dismissed'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-eye"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Read</span>
                                        <span class="info-box-number">{{ $stats['read'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="info-box">
                                    <span class="info-box-icon bg-secondary"><i class="fas fa-calendar"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Recent (7d)</span>
                                        <span class="info-box-number">{{ $stats['recent'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alerts Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                        </th>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Message</th>
                                        <th>User</th>
                                        <th>Alert Type</th>
                                        <th>Theme</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alerts as $alert)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="alert-checkbox"
                                                    value="{{ $alert->id }}">
                                            </td>
                                            <td>{{ $alert->id }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $alert->type === 'error' ? 'danger' : $alert->type }}">
                                                    {{ ucfirst($alert->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;"
                                                    title="{{ $alert->message }}">
                                                    {{ $alert->message }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($alert->user)
                                                    {{ $alert->user->name ?? $alert->user->email }}
                                                @else
                                                    <span class="text-muted">Guest</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ ucfirst($alert->alert_type) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ ucfirst($alert->theme) }}</span>
                                            </td>
                                            <td>
                                                @if ($alert->is_active && !$alert->isExpired() && !$alert->isDismissed())
                                                    <span class="badge badge-success">Active</span>
                                                @elseif($alert->isExpired())
                                                    <span class="badge badge-warning">Expired</span>
                                                @elseif($alert->isDismissed())
                                                    <span class="badge badge-danger">Dismissed</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($alert->priority > 0)
                                                    <span class="badge badge-warning">High</span>
                                                @else
                                                    <span class="badge badge-light">Normal</span>
                                                @endif
                                            </td>
                                            <td>{{ $alert->created_at->diffForHumans() }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('laravel-alert.admin.show', $alert->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('laravel-alert.admin.edit', $alert->id) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="deleteAlert({{ $alert->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="button" class="btn btn-sm btn-primary" onclick="bulkAction('activate')">
                                    <i class="fas fa-check"></i> Activate Selected
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('deactivate')">
                                    <i class="fas fa-pause"></i> Deactivate Selected
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                                    <i class="fas fa-trash"></i> Delete Selected
                                </button>
                            </div>
                            <div>
                                {{ $alerts->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.alert-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }

        function deleteAlert(id) {
            if (confirm('Are you sure you want to delete this alert?')) {
                fetch(`/laravel-alert/admin/alerts/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
            }
        }

        function bulkAction(action) {
            const checkboxes = document.querySelectorAll('.alert-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);

            if (ids.length === 0) {
                alert('Please select at least one alert.');
                return;
            }

            if (confirm(`Are you sure you want to ${action} ${ids.length} alert(s)?`)) {
                fetch('/laravel-alert/admin/alerts/bulk-update', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ids: ids,
                            action: action
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
            }
        }

        function exportAlerts(format) {
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '/laravel-alert/admin/export';

            const formatInput = document.createElement('input');
            formatInput.type = 'hidden';
            formatInput.name = 'format';
            formatInput.value = format;
            form.appendChild(formatInput);

            // Add current filters
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.forEach((value, key) => {
                if (key !== 'format') {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                }
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    </script>
@endsection
