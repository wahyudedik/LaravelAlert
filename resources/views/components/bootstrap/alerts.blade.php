@props(['alerts', 'config'])

@if ($hasAlerts())
    <div class="{{ $getContainerClasses() }}" style="{{ $getContainerStyles() }}" {!! $getContainerDataAttributes() !!}>
        @foreach ($alerts as $alert)
            @if ($alert->isValid())
                <x-alert::alert :type="$alert->getType()" :message="$alert->getMessage()" :title="$alert->getTitle()" :options="array_merge($alert->getOptions(), [
                    'class' => $alert->getClass(),
                    'style' => $alert->getStyle(),
                    'icon' => $alert->getIcon(),
                    'dismissible' => $alert->isDismissible(),
                    'expires_at' => $alert->getExpiresAt(),
                    'auto_dismiss_delay' => $alert->getAutoDismissDelay(),
                    'animation' => $alert->getAnimation(),
                    'position' => $alert->getPosition(),
                    'theme' => $alert->getTheme(),
                    'data_attributes' => $alert->getDataAttributes(),
                    'html_content' => $alert->getHtmlContent(),
                ])" />
            @endif
        @endforeach
    </div>
@endif

@if ($config['javascript']['enabled'] ?? true)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.laravel-alerts-container');
            if (container) {
                // Handle container animations
                if (container.dataset.animation) {
                    container.classList.add(`alerts-${container.dataset.animation}`);
                }

                // Handle position changes
                if (container.dataset.position) {
                    container.setAttribute('data-position', container.dataset.position);
                }

                // Handle max alerts limit
                const maxAlerts = parseInt(container.dataset.maxAlerts) || 5;
                const alerts = container.querySelectorAll('.alert');
                if (alerts.length > maxAlerts) {
                    for (let i = maxAlerts; i < alerts.length; i++) {
                        alerts[i].remove();
                    }
                }

                // Add global alert management
                window.LaravelAlert = {
                    dismiss: function(alertId) {
                        const alert = document.getElementById(alertId);
                        if (alert) {
                            alert.style.transition = 'opacity 0.3s ease';
                            alert.style.opacity = '0';
                            setTimeout(() => {
                                if (alert && alert.parentNode) {
                                    alert.remove();
                                }
                            }, 300);
                        }
                    },
                    dismissAll: function() {
                        const alerts = container.querySelectorAll('.alert');
                        alerts.forEach(alert => {
                            alert.style.transition = 'opacity 0.3s ease';
                            alert.style.opacity = '0';
                            setTimeout(() => {
                                if (alert && alert.parentNode) {
                                    alert.remove();
                                }
                            }, 300);
                        });
                    },
                    getAlerts: function() {
                        return container.querySelectorAll('.alert');
                    },
                    getAlertsCount: function() {
                        return container.querySelectorAll('.alert').length;
                    }
                };
            }
        });
    </script>
@endif
