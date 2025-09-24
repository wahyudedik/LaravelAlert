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

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure cache settings for alert storage and performance.
    |
    */
    'cache' => [
        'enabled' => env('LARAVEL_ALERT_CACHE_ENABLED', true),
        'driver' => env('LARAVEL_ALERT_CACHE_DRIVER', 'file'),
        'prefix' => env('LARAVEL_ALERT_CACHE_PREFIX', 'laravel_alert'),
        'ttl' => env('LARAVEL_ALERT_CACHE_TTL', 3600), // 1 hour
        'compression' => env('LARAVEL_ALERT_CACHE_COMPRESSION', false),
        'serialization' => env('LARAVEL_ALERT_CACHE_SERIALIZATION', 'json'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Redis settings for high-traffic applications.
    |
    */
    'redis' => [
        'enabled' => env('LARAVEL_ALERT_REDIS_ENABLED', false),
        'connection' => env('LARAVEL_ALERT_REDIS_CONNECTION', 'default'),
        'prefix' => env('LARAVEL_ALERT_REDIS_PREFIX', 'laravel_alert'),
        'ttl' => env('LARAVEL_ALERT_REDIS_TTL', 3600), // 1 hour
        'compression' => env('LARAVEL_ALERT_REDIS_COMPRESSION', true),
        'serialization' => env('LARAVEL_ALERT_REDIS_SERIALIZATION', 'json'),
        'cluster' => env('LARAVEL_ALERT_REDIS_CLUSTER', false),
        'options' => [
            'prefix' => env('LARAVEL_ALERT_REDIS_PREFIX', 'laravel_alert:'),
            'serializer' => \Redis::SERIALIZER_JSON,
            'compression' => \Redis::COMPRESSION_LZ4,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Configure performance optimizations for alert management.
    |
    */
    'performance' => [
        'batch_processing' => env('LARAVEL_ALERT_BATCH_PROCESSING', true),
        'lazy_loading' => env('LARAVEL_ALERT_LAZY_LOADING', true),
        'query_optimization' => env('LARAVEL_ALERT_QUERY_OPTIMIZATION', true),
        'cache_warming' => env('LARAVEL_ALERT_CACHE_WARMING', true),
        'index_optimization' => env('LARAVEL_ALERT_INDEX_OPTIMIZATION', true),
        'memory_optimization' => env('LARAVEL_ALERT_MEMORY_OPTIMIZATION', true),
        'connection_pooling' => env('LARAVEL_ALERT_CONNECTION_POOLING', true),
        'compression' => env('LARAVEL_ALERT_COMPRESSION', true),
        'max_batch_size' => env('LARAVEL_ALERT_MAX_BATCH_SIZE', 100),
        'chunk_size' => env('LARAVEL_ALERT_CHUNK_SIZE', 50),
        'memory_limit' => env('LARAVEL_ALERT_MEMORY_LIMIT', '128M'),
        'timeout' => env('LARAVEL_ALERT_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configure storage backend for alerts.
    |
    */
    'storage' => [
        'driver' => env('LARAVEL_ALERT_STORAGE_DRIVER', 'database'), // database, redis, cache, session
        'fallback' => env('LARAVEL_ALERT_STORAGE_FALLBACK', 'session'),
        'persistence' => env('LARAVEL_ALERT_STORAGE_PERSISTENCE', true),
        'cleanup_interval' => env('LARAVEL_ALERT_CLEANUP_INTERVAL', 3600), // 1 hour
        'max_alerts_per_user' => env('LARAVEL_ALERT_MAX_ALERTS_PER_USER', 1000),
        'max_alerts_per_session' => env('LARAVEL_ALERT_MAX_ALERTS_PER_SESSION', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pusher Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Pusher for real-time alerts.
    |
    */
    'pusher' => [
        'enabled' => env('LARAVEL_ALERT_PUSHER_ENABLED', false),
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
        'use_tls' => env('PUSHER_APP_USE_TLS', true),
        'encrypted' => env('PUSHER_APP_ENCRYPTED', true),
        'host' => env('PUSHER_APP_HOST'),
        'port' => env('PUSHER_APP_PORT'),
        'scheme' => env('PUSHER_APP_SCHEME', 'https'),
        'timeout' => env('PUSHER_APP_TIMEOUT', 30),
        'curl_options' => [],
        'default_channel' => env('LARAVEL_ALERT_PUSHER_DEFAULT_CHANNEL', 'alerts'),
        'user_channel_prefix' => env('LARAVEL_ALERT_PUSHER_USER_CHANNEL_PREFIX', 'user-alerts'),
        'session_channel_prefix' => env('LARAVEL_ALERT_PUSHER_SESSION_CHANNEL_PREFIX', 'session-alerts'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WebSocket Configuration
    |--------------------------------------------------------------------------
    |
    | Configure WebSocket for real-time alerts.
    |
    */
    'websocket' => [
        'enabled' => env('LARAVEL_ALERT_WEBSOCKET_ENABLED', false),
        'driver' => env('LARAVEL_ALERT_WEBSOCKET_DRIVER', 'redis'), // redis, cache, database
        'host' => env('LARAVEL_ALERT_WEBSOCKET_HOST', 'localhost'),
        'port' => env('LARAVEL_ALERT_WEBSOCKET_PORT', 8080),
        'secure' => env('LARAVEL_ALERT_WEBSOCKET_SECURE', false),
        'timeout' => env('LARAVEL_ALERT_WEBSOCKET_TIMEOUT', 30),
        'ttl' => env('LARAVEL_ALERT_WEBSOCKET_TTL', 3600),
        'subscription_ttl' => env('LARAVEL_ALERT_WEBSOCKET_SUBSCRIPTION_TTL', 3600),
        'key_prefix' => env('LARAVEL_ALERT_WEBSOCKET_KEY_PREFIX', 'websocket'),
        'default_channel' => env('LARAVEL_ALERT_WEBSOCKET_DEFAULT_CHANNEL', 'alerts'),
        'user_channel_prefix' => env('LARAVEL_ALERT_WEBSOCKET_USER_CHANNEL_PREFIX', 'user-alerts'),
        'session_channel_prefix' => env('LARAVEL_ALERT_WEBSOCKET_SESSION_CHANNEL_PREFIX', 'session-alerts'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    |
    | Configure email notifications for alerts.
    |
    */
    'email' => [
        'enabled' => env('LARAVEL_ALERT_EMAIL_ENABLED', false),
        'driver' => env('MAIL_MAILER', 'smtp'),
        'default_recipients' => env('LARAVEL_ALERT_EMAIL_DEFAULT_RECIPIENTS', ''),
        'default_template' => env('LARAVEL_ALERT_EMAIL_DEFAULT_TEMPLATE', 'laravel-alert::emails.alert'),
        'subject_prefix' => env('LARAVEL_ALERT_EMAIL_SUBJECT_PREFIX', '[Alert] '),
        'user_model' => env('LARAVEL_ALERT_EMAIL_USER_MODEL', 'App\\Models\\User'),
        'templates' => [
            'alert' => 'laravel-alert::emails.alert',
            'multiple' => 'laravel-alert::emails.multiple-alerts',
            'summary' => 'laravel-alert::emails.summary',
            'test' => 'laravel-alert::emails.test',
        ],
        'scheduling' => [
            'enabled' => env('LARAVEL_ALERT_EMAIL_SCHEDULING_ENABLED', false),
            'frequency' => env('LARAVEL_ALERT_EMAIL_SCHEDULING_FREQUENCY', 'daily'), // daily, weekly, monthly
            'time' => env('LARAVEL_ALERT_EMAIL_SCHEDULING_TIME', '09:00'),
            'timezone' => env('LARAVEL_ALERT_EMAIL_SCHEDULING_TIMEZONE', 'UTC'),
        ],
    ],
];
