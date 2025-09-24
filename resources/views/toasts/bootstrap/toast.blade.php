@props(['toast', 'config'])

<div id="{{ $toast->getId() }}"
    class="toast {{ $toast->getAllClasses() }} {{ $toast->isDismissible() ? 'toast-dismissible' : '' }}"
    @if ($toast->getStyle()) style="{{ $toast->getStyle() }}" @endif role="alert"
    data-toast-type="{{ $toast->getType() }}" data-toast-id="{{ $toast->getId() }}"
    @if ($toast->shouldAutoDismiss()) data-auto-dismiss="true"
        data-dismiss-delay="{{ $toast->getAutoDismissDelay() }}"
    @elseif($config['toast_auto_dismiss'] ?? false)
        data-auto-dismiss="true"
        data-dismiss-delay="{{ $config['toast_auto_dismiss_delay'] ?? 5000 }}" @endif
    @if ($toast->getExpiresAt()) data-expires-at="{{ $toast->getExpiresAt() }}" @endif
    @if ($toast->getAnimation()) data-animation="{{ $toast->getAnimation() }}" @endif
    @if ($toast->getPosition()) data-position="{{ $toast->getPosition() }}" @endif
    @if ($toast->getTheme()) data-theme="{{ $toast->getTheme() }}" @endif {!! $toast->getDataAttributesHtml() !!}>
    <div class="toast-header">
        @if ($toast->getIcon())
            <i class="{{ $toast->getIcon() }} me-2"></i>
        @else
            @switch($toast->getType())
                @case('success')
                    <i class="fas fa-check-circle text-success me-2"></i>
                @break

                @case('error')
                    <i class="fas fa-exclamation-circle text-danger me-2"></i>
                @break

                @case('warning')
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                @break

                @default
                    <i class="fas fa-info-circle text-info me-2"></i>
            @endswitch
        @endif

        @if ($toast->getTitle())
            <strong class="me-auto">{{ $toast->getTitle() }}</strong>
        @else
            <strong class="me-auto">{{ ucfirst($toast->getType()) }}</strong>
        @endif

        @if ($toast->shouldAutoDismiss() && $toast->getOption('show_progress', true))
            <div class="toast-progress">
                <div class="toast-progress-bar" data-duration="{{ $toast->getAutoDismissDelay() }}"></div>
            </div>
        @endif

        @if ($toast->isDismissible())
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"
                onclick="this.closest('.toast').remove()"></button>
        @endif
    </div>

    <div class="toast-body">
        @if ($toast->getHtmlContent())
            {!! $toast->getHtmlContent() !!}
        @else
            {{ $toast->getMessage() }}
        @endif
    </div>
</div>

@if ($config['javascript']['enabled'] ?? true)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('{{ $toast->getId() }}');
            if (toast) {
                // Handle auto-dismiss
                if (toast.dataset.autoDismiss === 'true') {
                    const delay = parseInt(toast.dataset.dismissDelay) ||
                        {{ $config['toast_auto_dismiss_delay'] ?? 5000 }};

                    // Start progress bar
                    const progressBar = toast.querySelector('.toast-progress-bar');
                    if (progressBar) {
                        const duration = parseInt(progressBar.dataset.duration);
                        progressBar.style.transition = `width ${duration}ms linear`;
                        progressBar.style.width = '0%';
                        setTimeout(() => {
                            progressBar.style.width = '100%';
                        }, 10);
                    }

                    setTimeout(function() {
                        if (toast && toast.parentNode) {
                            toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            toast.style.opacity = '0';
                            toast.style.transform = 'translateX(100%)';
                            setTimeout(function() {
                                if (toast && toast.parentNode) {
                                    toast.remove();
                                }
                            }, 300);
                        }
                    }, delay);
                }

                // Handle expiration
                if (toast.dataset.expiresAt) {
                    const expiresAt = parseInt(toast.dataset.expiresAt);
                    const now = Math.floor(Date.now() / 1000);
                    if (expiresAt <= now) {
                        toast.remove();
                        return;
                    }

                    // Check expiration every minute
                    setInterval(function() {
                        const now = Math.floor(Date.now() / 1000);
                        if (expiresAt <= now) {
                            toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            toast.style.opacity = '0';
                            toast.style.transform = 'translateX(100%)';
                            setTimeout(function() {
                                if (toast && toast.parentNode) {
                                    toast.remove();
                                }
                            }, 300);
                        }
                    }, 60000);
                }

                // Handle animations
                if (toast.dataset.animation) {
                    const animation = toast.dataset.animation;

                    switch (animation) {
                        case 'slide':
                            toast.style.transform = 'translateX(100%)';
                            toast.style.transition = 'transform 0.3s ease';
                            setTimeout(() => {
                                toast.style.transform = 'translateX(0)';
                            }, 10);
                            break;
                        case 'fade':
                        default:
                            toast.style.opacity = '0';
                            toast.style.transition = 'opacity 0.3s ease';
                            setTimeout(() => {
                                toast.style.opacity = '1';
                            }, 10);
                            break;
                    }
                }
            }
        });
    </script>
@endif
