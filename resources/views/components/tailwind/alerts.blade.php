@props(['alerts', 'config'])

@if (count($alerts) > 0)
    <div class="laravel-alerts-container" data-position="{{ $config['position'] ?? 'top-right' }}"
        data-max-alerts="{{ $config['max_alerts'] ?? 5 }}"
        style="position: fixed; z-index: 9999; {{ $config['position'] === 'top-right' ? 'top: 20px; right: 20px;' : '' }}{{ $config['position'] === 'top-left' ? 'top: 20px; left: 20px;' : '' }}{{ $config['position'] === 'bottom-right' ? 'bottom: 20px; right: 20px;' : '' }}{{ $config['position'] === 'bottom-left' ? 'bottom: 20px; left: 20px;' : '' }}{{ $config['position'] === 'top-center' ? 'top: 20px; left: 50%; transform: translateX(-50%);' : '' }}{{ $config['position'] === 'bottom-center' ? 'bottom: 20px; left: 50%; transform: translateX(-50%);' : '' }}">
        @foreach ($alerts as $alert)
            <x-alert::alert :type="$alert->getType()" :message="$alert->getMessage()" :title="$alert->getTitle()" :options="array_merge($alert->getOptions(), [
                'class' => $alert->getClass(),
                'style' => $alert->getStyle(),
                'icon' => $alert->getIcon(),
                'dismissible' => $alert->isDismissible(),
            ])" />
        @endforeach
    </div>
@endif
