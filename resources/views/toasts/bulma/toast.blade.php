@props(['toast', 'config'])

<div id="{{ $toast->getId() }}"
    class="notification {{ $toast->getAllClasses() }} {{ $toast->isDismissible() ? 'is-dismissible' : '' }}"
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

    <div class="notification-header">
        <div class="level">
            <div class="level-left">
                <div class="level-item">
                    @if ($toast->getIcon())
                        <span class="icon">
                            <i class="{{ $toast->getIcon() }}"></i>
                        </span>
                    @else
                        @switch($toast->getType())
                            @case('success')
                                <span class="icon has-text-success">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            @break

                            @case('error')
                                <span class="icon has-text-danger">
                                    <i class="fas fa-exclamation-circle"></i>
                                </span>
                            @break

                            @case('warning')
                                <span class="icon has-text-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </span>
                            @break

                            @default
                                <span class="icon has-text-info">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                        @endswitch
                    @endif

                    @if ($toast->getTitle())
                        <strong>{{ $toast->getTitle() }}</strong>
                    @else
                        <strong>{{ ucfirst($toast->getType()) }}</strong>
                    @endif
                </div>
            </div>

            <div class="level-right">
                @if ($toast->shouldAutoDismiss() && $toast->getOption('show_progress', true))
                    <div class="level-item">
                        <div class="toast-progress">
                            <progress class="progress is-small" value="0" max="100"
                                data-duration="{{ $toast->getAutoDismissDelay() }}"></progress>
                        </div>
                    </div>
                @endif

                @if ($toast->isDismissible())
                    <div class="level-item">
                        <button class="delete" onclick="this.closest('.notification').remove()"></button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="notification-content">
        @if ($toast->getHtmlContent())
            {!! $toast->getHtmlContent() !!}
        @else
            <p>{{ $toast->getMessage() }}</p>
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
                    const progressBar = toast.querySelector('progress');
                    if (progressBar) {
                        const duration = parseInt(progressBar.dataset.duration);
                        progressBar.style.transition = `value ${duration}ms linear`;
                        progressBar.value = 0;
                        setTimeout(() => {
                            progressBar.value = 100;
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
