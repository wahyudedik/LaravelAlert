<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    |
    | This option controls the default theme for alerts. You can choose from:
    | 'bootstrap', 'tailwind', 'bulma', or 'custom'
    |
    */
    'default_theme' => env('LARAVEL_ALERT_THEME', 'bootstrap'),

    /*
    |--------------------------------------------------------------------------
    | Auto Dismiss
    |--------------------------------------------------------------------------
    |
    | This option controls whether alerts should be automatically dismissed
    | after a certain period of time.
    |
    */
    'auto_dismiss' => env('LARAVEL_ALERT_AUTO_DISMISS', true),

    /*
    |--------------------------------------------------------------------------
    | Dismiss Delay
    |--------------------------------------------------------------------------
    |
    | This option controls the delay in milliseconds before alerts are
    | automatically dismissed.
    |
    */
    'dismiss_delay' => env('LARAVEL_ALERT_DISMISS_DELAY', 5000),

    /*
    |--------------------------------------------------------------------------
    | Animation
    |--------------------------------------------------------------------------
    |
    | This option controls the animation type for alerts. You can choose from:
    | 'fade', 'slide', 'bounce', or 'none'
    |
    */
    'animation' => env('LARAVEL_ALERT_ANIMATION', 'fade'),

    /*
    |--------------------------------------------------------------------------
    | Position
    |--------------------------------------------------------------------------
    |
    | This option controls the position of alerts on the page. You can choose from:
    | 'top-right', 'top-left', 'bottom-right', 'bottom-left', 'top-center', 'bottom-center'
    |
    */
    'position' => env('LARAVEL_ALERT_POSITION', 'top-right'),

    /*
    |--------------------------------------------------------------------------
    | Max Alerts
    |--------------------------------------------------------------------------
    |
    | This option controls the maximum number of alerts that can be displayed
    | at the same time.
    |
    */
    'max_alerts' => env('LARAVEL_ALERT_MAX_ALERTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Session Key
    |--------------------------------------------------------------------------
    |
    | This option controls the session key used to store alerts.
    |
    */
    'session_key' => env('LARAVEL_ALERT_SESSION_KEY', 'laravel_alerts'),

    /*
    |--------------------------------------------------------------------------
    | Themes Configuration
    |--------------------------------------------------------------------------
    |
    | This option contains the configuration for different themes.
    |
    */
    'themes' => [
        'bootstrap' => [
            'alert_class' => 'alert',
            'types' => [
                'success' => 'alert-success',
                'error' => 'alert-danger',
                'warning' => 'alert-warning',
                'info' => 'alert-info',
            ],
        ],
        'tailwind' => [
            'alert_class' => 'rounded-md p-4',
            'types' => [
                'success' => 'bg-green-50 text-green-800 border-green-200',
                'error' => 'bg-red-50 text-red-800 border-red-200',
                'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                'info' => 'bg-blue-50 text-blue-800 border-blue-200',
            ],
        ],
        'bulma' => [
            'alert_class' => 'notification',
            'types' => [
                'success' => 'is-success',
                'error' => 'is-danger',
                'warning' => 'is-warning',
                'info' => 'is-info',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | JavaScript Configuration
    |--------------------------------------------------------------------------
    |
    | This option controls JavaScript behavior for alerts.
    |
    */
    'javascript' => [
        'enabled' => env('LARAVEL_ALERT_JS_ENABLED', true),
        'auto_dismiss' => env('LARAVEL_ALERT_JS_AUTO_DISMISS', true),
        'dismiss_delay' => env('LARAVEL_ALERT_JS_DISMISS_DELAY', 5000),
        'animation_duration' => env('LARAVEL_ALERT_JS_ANIMATION_DURATION', 300),
    ],
];
