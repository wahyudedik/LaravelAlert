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

    <div class="toast-header flex items-center justify-between p-4 border-b border-gray-200">
        <div class="flex items-center">
            @if ($toast->getIcon())
                <i class="{{ $toast->getIcon() }} mr-2"></i>
            @else
                @switch($toast->getType())
                    @case('success')
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    @break

                    @case('error')
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    @break

                    @case('warning')
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    @break

                    @default
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                @endswitch
            @endif

            @if ($toast->getTitle())
                <strong class="font-semibold">{{ $toast->getTitle() }}</strong>
            @else
                <strong class="font-semibold">{{ ucfirst($toast->getType()) }}</strong>
            @endif
        </div>

        @if ($toast->shouldAutoDismiss() && $toast->getOption('show_progress', true))
            <div class="toast-progress ml-4">
                <div class="toast-progress-bar w-full h-1 bg-gray-200 rounded-full overflow-hidden"
                    data-duration="{{ $toast->getAutoDismissDelay() }}">
                    <div class="progress-fill h-full bg-gray-400 transition-all duration-300 ease-linear"></div>
                </div>
            </div>
        @endif

        @if ($toast->isDismissible())
            <button type="button" class="ml-4 text-gray-400 hover:text-gray-600 focus:outline-none"
                onclick="this.closest('.toast').remove()">
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>

    <div class="toast-body p-4">
        @if ($toast->getHtmlContent())
            {!! $toast->getHtmlContent() !!}
        @else
            <p class="text-gray-700">{{ $toast->getMessage() }}</p>
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
                    const progressBar = toast.querySelector('.progress-fill');
                    if (progressBar) {
                        const duration = parseInt(toast.querySelector('.toast-progress-bar').dataset.duration);
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
