@props(['toasts', 'config'])

@if (count($toasts) > 0)
    <div class="toast-container fixed z-50 {{ $this->getPositionClasses() }}"
        data-position="{{ $config['toast_position'] ?? 'top-right' }}"
        data-max-toasts="{{ $config['toast_max_toasts'] ?? 5 }}" data-stack="{{ $config['toast_stack'] ?? true }}">
        @foreach ($toasts as $toast)
            @if ($toast->isValid())
                <div
                    class="toast mb-4 max-w-sm w-full bg-white shadow-lg rounded-lg border border-gray-200 overflow-hidden {{ $this->getToastClasses($toast) }}">
                    <x-laravel-alert::toast :type="$toast->getType()" :message="$toast->getMessage()" :title="$toast->getTitle()" :options="array_merge($toast->getOptions(), [
                        'class' => $toast->getClass(),
                        'style' => $toast->getStyle(),
                        'icon' => $toast->getIcon(),
                        'dismissible' => $toast->isDismissible(),
                        'expires_at' => $toast->getExpiresAt(),
                        'auto_dismiss_delay' => $toast->getAutoDismissDelay(),
                        'animation' => $toast->getAnimation(),
                        'position' => $toast->getPosition(),
                        'theme' => $toast->getTheme(),
                        'data_attributes' => $toast->getDataAttributes(),
                        'html_content' => $toast->getHtmlContent(),
                        'show_progress' => $toast->getOption('show_progress', true),
                        'stack' => $toast->getOption('stack', true),
                    ])" />
                </div>
            @endif
        @endforeach
    </div>
@endif

@if ($config['javascript']['enabled'] ?? true)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.toast-container');
            if (container) {
                // Handle stacking
                if (container.dataset.stack === 'true') {
                    const toasts = container.querySelectorAll('.toast');
                    toasts.forEach((toast, index) => {
                        toast.style.marginBottom = '16px';
                        toast.style.transform = `translateY(${index * 8}px)`;
                    });
                }

                // Handle max toasts limit
                const maxToasts = parseInt(container.dataset.maxToasts) || 5;
                const toasts = container.querySelectorAll('.toast');
                if (toasts.length > maxToasts) {
                    for (let i = maxToasts; i < toasts.length; i++) {
                        toasts[i].remove();
                    }
                }

                // Add global toast management
                window.LaravelToast = {
                    dismiss: function(toastId) {
                        const toast = document.getElementById(toastId);
                        if (toast) {
                            toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            toast.style.opacity = '0';
                            toast.style.transform = 'translateX(100%)';
                            setTimeout(() => {
                                if (toast && toast.parentNode) {
                                    toast.remove();
                                }
                            }, 300);
                        }
                    },
                    dismissAll: function() {
                        const toasts = container.querySelectorAll('.toast');
                        toasts.forEach(toast => {
                            toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            toast.style.opacity = '0';
                            toast.style.transform = 'translateX(100%)';
                            setTimeout(() => {
                                if (toast && toast.parentNode) {
                                    toast.remove();
                                }
                            }, 300);
                        });
                    },
                    getToasts: function() {
                        return container.querySelectorAll('.toast');
                    },
                    getToastsCount: function() {
                        return container.querySelectorAll('.toast').length;
                    }
                };
            }
        });
    </script>
@endif

@php
    function getPositionClasses()
    {
        $position = $config['toast_position'] ?? 'top-right';
        switch ($position) {
            case 'top-right':
                return 'top-4 right-4';
            case 'top-left':
                return 'top-4 left-4';
            case 'bottom-right':
                return 'bottom-4 right-4';
            case 'bottom-left':
                return 'bottom-4 left-4';
            case 'top-center':
                return 'top-4 left-1/2 transform -translate-x-1/2';
            case 'bottom-center':
                return 'bottom-4 left-1/2 transform -translate-x-1/2';
            default:
                return 'top-4 right-4';
        }
    }

    function getToastClasses($toast)
    {
        $classes = [];

        switch ($toast->getType()) {
            case 'success':
                $classes[] = 'border-green-200 bg-green-50';
                break;
            case 'error':
                $classes[] = 'border-red-200 bg-red-50';
                break;
            case 'warning':
                $classes[] = 'border-yellow-200 bg-yellow-50';
                break;
            default:
                $classes[] = 'border-blue-200 bg-blue-50';
        }

        if ($toast->getAnimation()) {
            $classes[] = 'animate-' . $toast->getAnimation();
        }

        return implode(' ', $classes);
    }
@endphp
