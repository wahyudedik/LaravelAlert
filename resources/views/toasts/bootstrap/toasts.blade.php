@props(['toasts', 'config'])

@if (count($toasts) > 0)
    <div class="toast-container position-fixed" style="z-index: 9999; {{ $this->getPositionStyles() }}"
        data-position="{{ $config['toast_position'] ?? 'top-right' }}"
        data-max-toasts="{{ $config['toast_max_toasts'] ?? 5 }}" data-stack="{{ $config['toast_stack'] ?? true }}">
        @foreach ($toasts as $toast)
            @if ($toast->isValid())
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
                        toast.style.marginBottom = '10px';
                        toast.style.transform = `translateY(${index * 10}px)`;
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
    function getPositionStyles()
    {
        $position = $config['toast_position'] ?? 'top-right';
        switch ($position) {
            case 'top-right':
                return 'top: 20px; right: 20px;';
            case 'top-left':
                return 'top: 20px; left: 20px;';
            case 'bottom-right':
                return 'bottom: 20px; right: 20px;';
            case 'bottom-left':
                return 'bottom: 20px; left: 20px;';
            case 'top-center':
                return 'top: 20px; left: 50%; transform: translateX(-50%);';
            case 'bottom-center':
                return 'bottom: 20px; left: 50%; transform: translateX(-50%);';
            default:
                return 'top: 20px; right: 20px;';
        }
    }
@endphp
