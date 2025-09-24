@props(['alert', 'config'])

<div id="{{ $alert->getId() }}"
    class="alert alert-{{ $alert->getType() === 'error' ? 'danger' : $alert->getType() }} {{ $alert->getClass() }} {{ $alert->isDismissible() ? 'alert-dismissible fade show' : '' }}"
    @if ($alert->getStyle()) style="{{ $alert->getStyle() }}" @endif role="alert"
    data-alert-type="{{ $alert->getType() }}" data-alert-id="{{ $alert->getId() }}"
    @if ($config['auto_dismiss'] ?? false) data-auto-dismiss="true"
        data-dismiss-delay="{{ $config['dismiss_delay'] ?? 5000 }}" @endif>
    @if ($alert->getIcon())
        <i class="{{ $alert->getIcon() }} me-2"></i>
    @endif

    @if ($alert->getTitle())
        <strong>{{ $alert->getTitle() }}</strong>
        <br>
    @endif

    {{ $alert->getMessage() }}

    @if ($alert->isDismissible())
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
            onclick="this.parentElement.remove()"></button>
    @endif
</div>

@if ($config['javascript']['enabled'] ?? true)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('{{ $alert->getId() }}');
            if (alert && alert.dataset.autoDismiss === 'true') {
                const delay = parseInt(alert.dataset.dismissDelay) || {{ $config['dismiss_delay'] ?? 5000 }};
                setTimeout(function() {
                    if (alert && alert.parentNode) {
                        alert.style.transition = 'opacity 0.3s ease';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            if (alert && alert.parentNode) {
                                alert.remove();
                            }
                        }, 300);
                    }
                }, delay);
            }
        });
    </script>
@endif
