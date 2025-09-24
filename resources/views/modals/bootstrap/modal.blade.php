@props(['modal', 'config'])

<div id="{{ $modal->getId() }}" class="modal fade {{ $modal->getAllClasses() }}"
    @if ($modal->getStyle()) style="{{ $modal->getStyle() }}" @endif tabindex="-1" role="dialog"
    aria-labelledby="{{ $modal->getId() }}Label" aria-hidden="true" data-modal-type="{{ $modal->getType() }}"
    data-modal-id="{{ $modal->getId() }}"
    @if ($modal->getOption('backdrop', true)) data-bs-backdrop="true"
    @else
        data-bs-backdrop="false" @endif
    @if ($modal->getOption('keyboard', true)) data-bs-keyboard="true"
    @else
        data-bs-keyboard="false" @endif
    @if ($modal->getOption('focus', true)) data-bs-focus="true"
    @else
        data-bs-focus="false" @endif
    @if ($modal->getExpiresAt()) data-expires-at="{{ $modal->getExpiresAt() }}" @endif
    @if ($modal->getAnimation()) data-animation="{{ $modal->getAnimation() }}" @endif
    @if ($modal->getTheme()) data-theme="{{ $modal->getTheme() }}" @endif {!! $modal->getDataAttributesHtml() !!}>
    <div
        class="modal-dialog {{ $modal->getOption('size', 'md') === 'sm' ? 'modal-sm' : '' }} {{ $modal->getOption('size', 'md') === 'lg' ? 'modal-lg' : '' }} {{ $modal->getOption('size', 'md') === 'xl' ? 'modal-xl' : '' }} {{ $modal->getOption('centered', true) ? 'modal-dialog-centered' : '' }}">
        <div class="modal-content">
            <div class="modal-header">
                @if ($modal->getIcon())
                    <i class="{{ $modal->getIcon() }} me-2"></i>
                @else
                    @switch($modal->getType())
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

                @if ($modal->getTitle())
                    <h5 class="modal-title" id="{{ $modal->getId() }}Label">{{ $modal->getTitle() }}</h5>
                @else
                    <h5 class="modal-title" id="{{ $modal->getId() }}Label">{{ ucfirst($modal->getType()) }}</h5>
                @endif

                @if ($modal->getOption('show_close_button', true))
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="this.closest('.modal').remove()"></button>
                @endif
            </div>

            <div class="modal-body">
                @if ($modal->getHtmlContent())
                    {!! $modal->getHtmlContent() !!}
                @else
                    {{ $modal->getMessage() }}
                @endif

                @if ($modal->getOption('input', false))
                    <div class="mt-3">
                        <label for="{{ $modal->getId() }}_input" class="form-label">Input:</label>
                        <input type="text" class="form-control" id="{{ $modal->getId() }}_input" name="modal_input"
                            placeholder="Enter your response...">
                    </div>
                @endif
            </div>

            @if ($modal->getOption('actions'))
                <div class="modal-footer">
                    @foreach ($modal->getOption('actions') as $actionKey => $action)
                        <button type="button" class="btn {{ $action['class'] ?? 'btn-secondary' }}"
                            data-action="{{ $action['action'] ?? $actionKey }}" data-modal-id="{{ $modal->getId() }}"
                            onclick="handleModalAction(this)">
                            {{ $action['text'] ?? ucfirst($actionKey) }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@if ($config['javascript']['enabled'] ?? true)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('{{ $modal->getId() }}');
            if (modal) {
                // Initialize Bootstrap modal
                const bsModal = new bootstrap.Modal(modal, {
                    backdrop: modal.dataset.bsBackdrop !== 'false',
                    keyboard: modal.dataset.bsKeyboard !== 'false',
                    focus: modal.dataset.bsFocus !== 'false'
                });

                // Show modal
                bsModal.show();

                // Handle expiration
                if (modal.dataset.expiresAt) {
                    const expiresAt = parseInt(modal.dataset.expiresAt);
                    const now = Math.floor(Date.now() / 1000);
                    if (expiresAt <= now) {
                        bsModal.hide();
                        return;
                    }

                    // Check expiration every minute
                    setInterval(function() {
                        const now = Math.floor(Date.now() / 1000);
                        if (expiresAt <= now) {
                            bsModal.hide();
                        }
                    }, 60000);
                }

                // Handle auto-dismiss
                if (modal.dataset.autoDismiss === 'true') {
                    const delay = parseInt(modal.dataset.dismissDelay) || 5000;
                    setTimeout(function() {
                        bsModal.hide();
                    }, delay);
                }

                // Handle modal events
                modal.addEventListener('hidden.bs.modal', function() {
                    modal.remove();
                });
            }
        });

        // Global modal action handler
        function handleModalAction(button) {
            const action = button.dataset.action;
            const modalId = button.dataset.modalId;
            const modal = document.getElementById(modalId);
            const input = modal.querySelector('input[name="modal_input"]');

            // Get input value if exists
            const inputValue = input ? input.value : null;

            // Dispatch custom event
            const event = new CustomEvent('laravel-alert-modal-action', {
                detail: {
                    action: action,
                    modalId: modalId,
                    input: inputValue,
                    button: button
                }
            });

            document.dispatchEvent(event);

            // Close modal
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                }
            }
        }
    </script>
@endif
