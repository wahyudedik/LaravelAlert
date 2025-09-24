@props(['alert', 'config'])

<div id="{{ $alert->getId() }}"
    class="rounded-md p-4 mb-4 {{ $alert->getClass() }} {{ $alert->isDismissible() ? 'relative' : '' }}"
    @if ($alert->getStyle()) style="{{ $alert->getStyle() }}" @endif role="alert"
    data-alert-type="{{ $alert->getType() }}" data-alert-id="{{ $alert->getId() }}"
    @if ($config['auto_dismiss'] ?? false) data-auto-dismiss="true"
        data-dismiss-delay="{{ $config['dismiss_delay'] ?? 5000 }}" @endif>
    <div class="flex">
        <div class="flex-shrink-0">
            @if ($alert->getIcon())
                <i class="{{ $alert->getIcon() }}"></i>
            @else
                @switch($alert->getType())
                    @case('success')
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    @break

                    @case('error')
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    @break

                    @case('warning')
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    @break

                    @default
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                @endswitch
            @endif
        </div>
        <div class="ml-3">
            @if ($alert->getTitle())
                <h3 class="text-sm font-medium">
                    {{ $alert->getTitle() }}
                </h3>
            @endif
            <div class="text-sm">
                {{ $alert->getMessage() }}
            </div>
        </div>
        @if ($alert->isDismissible())
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button"
                        class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
                        onclick="this.closest('[role=alert]').remove()">
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                </div>
            </div>
        @endif
    </div>
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
