@props(['toasts', 'config'])

@if (count($toasts) > 0)
    <div class="toast-container is-fixed {{ $this->getPositionClasses() }}" style="z-index: 9999;"
        data-position="{{ $config['toast_position'] ?? 'top-right' }}"
        data-max-toasts="{{ $config['toast_max_toasts'] ?? 5 }}" data-stack="{{ $config['toast_stack'] ?? true }}">
        @foreach ($toasts as $toast)
            @if ($toast->isValid())
                <div class="notification mb-4 {{ $this->getToastClasses($toast) }}" style="max-width: 400px;">
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
                    const toasts = container.querySelectorAll('.notification');
                    toasts.forEach((toast, index) => {
                        toast.style.marginBottom = '16px';
                        toast.style.transform = `translateY(${index * 8}px)`;
                    });
                }

                // Handle max toasts limit
                const maxToasts = parseInt(container.dataset.maxToasts) || 5;
                const toasts = container.querySelectorAll('.notification');
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
                        const toasts = container.querySelectorAll('.notification');
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
                        return container.querySelectorAll('.notification');
                    },
                    getToastsCount: function() {
                        return container.querySelectorAll('.notification').length;
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
                return 'is-top-right';
            case 'top-left':
                return 'is-top-left';
            case 'bottom-right':
                return 'is-bottom-right';
            case 'bottom-left':
                return 'is-bottom-left';
            case 'top-center':
                return 'is-top-center';
            case 'bottom-center':
                return 'is-bottom-center';
            default:
                return 'is-top-right';
        }
    }

    function getToastClasses($toast)
    {
        $classes = [];

        switch ($toast->getType()) {
            case 'success':
                $classes[] = 'is-success';
                break;
            case 'error':
                $classes[] = 'is-danger';
                break;
            case 'warning':
                $classes[] = 'is-warning';
                break;
            default:
                $classes[] = 'is-info';
        }

        if ($toast->getAnimation()) {
            $classes[] = 'has-animation-' . $toast->getAnimation();
        }

        return implode(' ', $classes);
    }
@endphp
