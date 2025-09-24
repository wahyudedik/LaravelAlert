@props(['alert', 'config'])

<div id="{{ $alert->getId() }}"
    class="notification {{ $alert->getAllClasses() }} {{ $alert->isDismissible() ? 'is-dismissible' : '' }}"
    @if ($alert->getStyle()) style="{{ $alert->getStyle() }}" @endif role="alert"
    data-alert-type="{{ $alert->getType() }}" data-alert-id="{{ $alert->getId() }}"
    @if ($alert->shouldAutoDismiss()) data-auto-dismiss="true"
        data-dismiss-delay="{{ $alert->getAutoDismissDelay() }}"
    @elseif($config['auto_dismiss'] ?? false)
        data-auto-dismiss="true"
        data-dismiss-delay="{{ $config['dismiss_delay'] ?? 5000 }}" @endif
    @if ($alert->getExpiresAt()) data-expires-at="{{ $alert->getExpiresAt() }}" @endif
    @if ($alert->getAnimation()) data-animation="{{ $alert->getAnimation() }}" @endif
    @if ($alert->getPosition()) data-position="{{ $alert->getPosition() }}" @endif
    @if ($alert->getTheme()) data-theme="{{ $alert->getTheme() }}" @endif {!! $alert->getDataAttributesHtml() !!}>
    @if ($alert->isDismissible())
        <button class="delete" onclick="this.parentElement.remove()" aria-label="delete"></button>
    @endif

    @if ($alert->getIcon())
        <span class="icon">
            <i class="{{ $alert->getIcon() }}"></i>
        </span>
    @endif

    <div class="content">
        @if ($alert->getTitle())
            <h4 class="title is-5">{{ $alert->getTitle() }}</h4>
        @endif

        @if ($alert->getHtmlContent())
            {!! $alert->getHtmlContent() !!}
        @else
            <p>{{ $alert->getMessage() }}</p>
        @endif
    </div>
</div>

@if ($config['javascript']['enabled'] ?? true)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('{{ $alert->getId() }}');
            if (alert) {
                // Handle auto-dismiss
                if (alert.dataset.autoDismiss === 'true') {
                    const delay = parseInt(alert.dataset.dismissDelay) || {{ $config['dismiss_delay'] ?? 5000 }};
                    setTimeout(function() {
                        if (alert && alert.parentNode) {
                            alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            alert.style.opacity = '0';
                            alert.style.transform = 'scale(0.8)';
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
                            alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            alert.style.opacity = '0';
                            alert.style.transform = 'scale(0.8)';
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
                        case 'bounce':
                            alert.style.animation = 'bounce 0.6s ease';
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
            }
        });
    </script>
@endif
