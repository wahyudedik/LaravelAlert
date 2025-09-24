@props(['alert', 'config'])

<div id="{{ $alert->getId() }}"
    class="alert alert-{{ $alert->getType() === 'error' ? 'danger' : $alert->getType() }} {{ $alert->getAllClasses() }} {{ $alert->isDismissible() ? 'alert-dismissible fade show' : '' }}"
    @if ($alert->getStyle()) style="{{ $alert->getStyle() }}" @endif role="alert"
    data-alert-type="{{ $alert->getType() }}" data-alert-id="{{ $alert->getId() }}"
    data-context="{{ $alert->getOption('context', 'general') }}"
    @if ($alert->getOption('field')) data-field="{{ $alert->getOption('field') }}" @endif
    @if ($alert->getOption('form')) data-form="{{ $alert->getOption('form') }}" @endif
    @if ($alert->shouldAutoDismiss()) data-auto-dismiss="true"
        data-dismiss-delay="{{ $alert->getAutoDismissDelay() }}" @endif
    @if ($alert->getExpiresAt()) data-expires-at="{{ $alert->getExpiresAt() }}" @endif
    @if ($alert->getAnimation()) data-animation="{{ $alert->getAnimation() }}" @endif
    @if ($alert->getOption('sticky')) data-sticky="true" @endif {!! $alert->getDataAttributesHtml() !!}>
    @if ($alert->getIcon())
        <i class="{{ $alert->getIcon() }} me-2"></i>
    @else
        @switch($alert->getType())
            @case('success')
                <i class="fas fa-check-circle me-2"></i>
            @break

            @case('error')
                <i class="fas fa-exclamation-circle me-2"></i>
            @break

            @case('warning')
                <i class="fas fa-exclamation-triangle me-2"></i>
            @break

            @default
                <i class="fas fa-info-circle me-2"></i>
        @endswitch
    @endif

    @if ($alert->getTitle())
        <strong>{{ $alert->getTitle() }}</strong>
        <br>
    @endif

    @if ($alert->getHtmlContent())
        {!! $alert->getHtmlContent() !!}
    @else
        {{ $alert->getMessage() }}
    @endif

    @if ($alert->isDismissible())
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
            onclick="this.parentElement.remove()"></button>
    @endif
</div>

@if ($config['javascript']['enabled'] ?? true)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('{{ $alert->getId() }}');
            if (alert) {
                // Handle auto-dismiss
                if (alert.dataset.autoDismiss === 'true') {
                    const delay = parseInt(alert.dataset.dismissDelay) || 5000;
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

                // Handle expiration
                if (alert.dataset.expiresAt) {
                    const expiresAt = parseInt(alert.dataset.expiresAt);
                    const now = Math.floor(Date.now() / 1000);
                    if (expiresAt <= now) {
                        alert.remove();
                        return;
                    }

                    // Check expiration every minute
                    setInterval(function() {
                        const now = Math.floor(Date.now() / 1000);
                        if (expiresAt <= now) {
                            alert.style.transition = 'opacity 0.3s ease';
                            alert.style.opacity = '0';
                            setTimeout(function() {
                                if (alert && alert.parentNode) {
                                    alert.remove();
                                }
                            }, 300);
                        }
                    }, 60000);
                }

                // Handle animations
                if (alert.dataset.animation) {
                    const animation = alert.dataset.animation;

                    switch (animation) {
                        case 'slide':
                            alert.style.transform = 'translateY(-100%)';
                            alert.style.transition = 'transform 0.3s ease';
                            setTimeout(() => {
                                alert.style.transform = 'translateY(0)';
                            }, 10);
                            break;
                        case 'fade':
                        default:
                            alert.style.opacity = '0';
                            alert.style.transition = 'opacity 0.3s ease';
                            setTimeout(() => {
                                alert.style.opacity = '1';
                            }, 10);
                            break;
                    }
                }

                // Handle field-specific alerts
                if (alert.dataset.field) {
                    const field = document.querySelector(
                    `[name="${alert.dataset.field}"], #${alert.dataset.field}`);
                    if (field) {
                        // Add error class to field
                        field.classList.add('is-invalid');

                        // Position alert near field
                        const fieldRect = field.getBoundingClientRect();
                        alert.style.position = 'absolute';
                        alert.style.top = (fieldRect.bottom + 5) + 'px';
                        alert.style.left = fieldRect.left + 'px';
                        alert.style.zIndex = '1000';
                        alert.style.maxWidth = fieldRect.width + 'px';
                    }
                }

                // Handle form alerts
                if (alert.dataset.form) {
                    const form = document.querySelector(`#${alert.dataset.form}, [name="${alert.dataset.form}"]`);
                    if (form) {
                        // Insert at the top of the form
                        form.insertBefore(alert, form.firstChild);
                    }
                }

                // Handle contextual alerts
                if (alert.dataset.context && alert.dataset.context !== 'general') {
                    const contextElement = document.querySelector(`[data-context="${alert.dataset.context}"]`);
                    if (contextElement) {
                        contextElement.appendChild(alert);
                    }
                }
            }
        });
    </script>
@endif
