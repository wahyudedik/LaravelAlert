<?php

/**
 * Laravel Alert Package Discovery Configuration
 * 
 * This file contains the auto-discovery configuration for Laravel Alert.
 * It ensures that the package is automatically discovered and registered
 * by Laravel without manual configuration.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Auto-Discovery Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the auto-discovery settings for Laravel Alert.
    | These settings are automatically detected by Laravel's package
    | discovery system.
    |
    */
    'auto_discovery' => [
        'enabled' => true,
        'providers' => [
            'Wahyudedik\\LaravelAlert\\AlertServiceProvider',
        ],
        'aliases' => [
            'Alert' => 'Wahyudedik\\LaravelAlert\\Facades\\Alert',
            'Toast' => 'Wahyudedik\\LaravelAlert\\Facades\\Toast',
            'Modal' => 'Wahyudedik\\LaravelAlert\\Facades\\Modal',
            'Inline' => 'Wahyudedik\\LaravelAlert\\Facades\\Inline',
        ],
        'commands' => [
            'Wahyudedik\\LaravelAlert\\Console\\Commands\\InstallCommand',
            'Wahyudedik\\LaravelAlert\\Console\\Commands\\PublishCommand',
            'Wahyudedik\\LaravelAlert\\Console\\Commands\\ClearCommand',
        ],
        'middleware' => [
            'alert' => 'Wahyudedik\\LaravelAlert\\Http\\Middleware\\AlertMiddleware',
            'laravel-alert.api.auth' => 'Wahyudedik\\LaravelAlert\\Http\\Middleware\\ApiAuthentication',
            'laravel-alert.admin.auth' => 'Wahyudedik\\LaravelAlert\\Http\\Middleware\\AdminAuthentication',
            'laravel-alert.webhook.auth' => 'Wahyudedik\\LaravelAlert\\Http\\Middleware\\WebhookAuthentication',
            'laravel-alert.cors' => 'Wahyudedik\\LaravelAlert\\Http\\Middleware\\CorsMiddleware',
        ],
        'routes' => [
            'api' => 'routes/api.php',
            'web' => 'routes/web.php',
        ],
        'views' => [
            'namespace' => 'laravel-alert',
            'path' => 'resources/views',
        ],
        'assets' => [
            'css' => [
                'resources/css/laravel-alert.css',
            ],
            'js' => [
                'resources/js/laravel-alert.js',
            ],
        ],
        'config' => [
            'laravel-alert' => 'config/laravel-alert.php',
        ],
        'migrations' => [
            'database/migrations',
        ],
        'translations' => [
            'lang' => 'resources/lang',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Provider Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the service provider registration settings.
    | These settings control how the service provider is registered
    | and what services it provides.
    |
    */
    'service_provider' => [
        'auto_register' => true,
        'deferred' => false,
        'priority' => 0,
        'provides' => [
            'laravel-alert.manager',
            'laravel-alert.toast',
            'laravel-alert.modal',
            'laravel-alert.inline',
            'laravel-alert.database',
            'laravel-alert.redis',
            'laravel-alert.cache',
            'laravel-alert.pusher',
            'laravel-alert.websocket',
            'laravel-alert.email',
            'laravel-alert.performance',
            'laravel-alert.animation',
        ],
        'singletons' => [
            'laravel-alert.manager',
            'laravel-alert.toast',
            'laravel-alert.modal',
            'laravel-alert.inline',
            'laravel-alert.database',
            'laravel-alert.redis',
            'laravel-alert.cache',
            'laravel-alert.pusher',
            'laravel-alert.websocket',
            'laravel-alert.email',
            'laravel-alert.performance',
            'laravel-alert.animation',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Facade Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the facade registration settings.
    | These settings control how facades are registered and
    | what services they provide access to.
    |
    */
    'facades' => [
        'auto_register' => true,
        'aliases' => [
            'Alert' => [
                'class' => 'Wahyudedik\\LaravelAlert\\Facades\\Alert',
                'service' => 'laravel-alert.manager',
            ],
            'Toast' => [
                'class' => 'Wahyudedik\\LaravelAlert\\Facades\\Toast',
                'service' => 'laravel-alert.toast',
            ],
            'Modal' => [
                'class' => 'Wahyudedik\\LaravelAlert\\Facades\\Modal',
                'service' => 'laravel-alert.modal',
            ],
            'Inline' => [
                'class' => 'Wahyudedik\\LaravelAlert\\Facades\\Inline',
                'service' => 'laravel-alert.inline',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Console Commands Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the console command registration settings.
    | These settings control how console commands are registered
    | and what they do.
    |
    */
    'commands' => [
        'auto_register' => true,
        'namespace' => 'laravel-alert',
        'commands' => [
            'install' => [
                'class' => 'Wahyudedik\\LaravelAlert\\Console\\Commands\\InstallCommand',
                'description' => 'Install Laravel Alert package',
                'help' => 'This command installs the Laravel Alert package and publishes its assets.',
            ],
            'publish' => [
                'class' => 'Wahyudedik\\LaravelAlert\\Console\\Commands\\PublishCommand',
                'description' => 'Publish Laravel Alert assets',
                'help' => 'This command publishes the Laravel Alert assets to your application.',
            ],
            'clear' => [
                'class' => 'Wahyudedik\\LaravelAlert\\Console\\Commands\\ClearCommand',
                'description' => 'Clear Laravel Alert data',
                'help' => 'This command clears all Laravel Alert data from storage.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the middleware registration settings.
    | These settings control how middleware is registered and
    | what it does.
    |
    */
    'middleware' => [
        'auto_register' => true,
        'global' => [
            'alert' => 'Wahyudedik\\LaravelAlert\\Http\\Middleware\\AlertMiddleware',
        ],
        'aliases' => [
            'alert' => 'Wahyudedik\\LaravelAlert\\Http\\Middleware\\AlertMiddleware',
            'laravel-alert.api.auth' => 'Wahyudedik\\LaravelAlert\\Http\\Middleware\\ApiAuthentication',
            'laravel-alert.admin.auth' => 'Wahyudedik\\LaravelAlert\\Http\\Middleware\\AdminAuthentication',
            'laravel-alert.webhook.auth' => 'Wahyudedik\\LaravelAlert\\Http\\Middleware\\WebhookAuthentication',
            'laravel-alert.cors' => 'Wahyudedik\\LaravelAlert\\Http\\Middleware\\CorsMiddleware',
        ],
        'groups' => [
            'web' => [
                'alert',
            ],
            'api' => [
                'laravel-alert.api.auth',
                'laravel-alert.cors',
            ],
            'admin' => [
                'laravel-alert.admin.auth',
            ],
            'webhook' => [
                'laravel-alert.webhook.auth',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the route registration settings.
    | These settings control how routes are registered and
    | what they provide.
    |
    */
    'routes' => [
        'auto_register' => true,
        'prefix' => 'laravel-alert',
        'middleware' => ['web'],
        'api' => [
            'prefix' => 'api/v1',
            'middleware' => ['api', 'laravel-alert.api.auth'],
            'namespace' => 'Wahyudedik\\LaravelAlert\\Http\\Controllers\\Api',
        ],
        'web' => [
            'prefix' => 'admin',
            'middleware' => ['web', 'laravel-alert.admin.auth'],
            'namespace' => 'Wahyudedik\\LaravelAlert\\Http\\Controllers\\Admin',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Views Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the view registration settings.
    | These settings control how views are registered and
    | what they provide.
    |
    */
    'views' => [
        'auto_register' => true,
        'namespace' => 'laravel-alert',
        'path' => 'resources/views',
        'components' => [
            'alert' => 'Wahyudedik\\LaravelAlert\\View\\Components\\AlertComponent',
            'alerts' => 'Wahyudedik\\LaravelAlert\\View\\Components\\AlertsComponent',
            'alert-toast' => 'Wahyudedik\\LaravelAlert\\View\\Components\\ToastComponent',
            'alert-modal' => 'Wahyudedik\\LaravelAlert\\View\\Components\\ModalComponent',
            'alert-inline' => 'Wahyudedik\\LaravelAlert\\View\\Components\\InlineComponent',
        ],
        'directives' => [
            'alert' => 'Wahyudedik\\LaravelAlert\\View\\Directives\\AlertDirective',
            'alerts' => 'Wahyudedik\\LaravelAlert\\View\\Directives\\AlertsDirective',
            'alertIf' => 'Wahyudedik\\LaravelAlert\\View\\Directives\\AlertIfDirective',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Assets Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the asset registration settings.
    | These settings control how assets are registered and
    | what they provide.
    |
    */
    'assets' => [
        'auto_register' => true,
        'css' => [
            'laravel-alert' => 'resources/css/laravel-alert.css',
            'laravel-alert-bootstrap' => 'resources/css/themes/bootstrap.css',
            'laravel-alert-tailwind' => 'resources/css/themes/tailwind.css',
            'laravel-alert-bulma' => 'resources/css/themes/bulma.css',
        ],
        'js' => [
            'laravel-alert' => 'resources/js/laravel-alert.js',
            'laravel-alert-ajax' => 'resources/js/ajax.js',
            'laravel-alert-websocket' => 'resources/js/websocket.js',
            'laravel-alert-pusher' => 'resources/js/pusher.js',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration Files
    |--------------------------------------------------------------------------
    |
    | This section defines the configuration file registration settings.
    | These settings control how configuration files are registered and
    | what they provide.
    |
    */
    'config' => [
        'auto_register' => true,
        'files' => [
            'laravel-alert' => 'config/laravel-alert.php',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the database registration settings.
    | These settings control how database files are registered and
    | what they provide.
    |
    */
    'database' => [
        'auto_register' => true,
        'migrations' => [
            'database/migrations',
        ],
        'seeders' => [
            'database/seeders',
        ],
        'factories' => [
            'database/factories',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Translations Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the translation registration settings.
    | These settings control how translations are registered and
    | what they provide.
    |
    */
    'translations' => [
        'auto_register' => true,
        'path' => 'resources/lang',
        'namespace' => 'laravel-alert',
    ],

    /*
    |--------------------------------------------------------------------------
    | Package Information
    |--------------------------------------------------------------------------
    |
    | This section contains package information that is used by
    | Laravel's package discovery system.
    |
    */
    'package' => [
        'name' => 'wahyudedik/laravel-alert',
        'description' => 'A comprehensive Laravel alert system with multiple types, themes, and integrations',
        'version' => '1.0.0',
        'author' => 'Wahyudedik',
        'email' => 'wahyudedik@gmail.com',
        'homepage' => 'https://github.com/wahyudedik/LaravelAlert',
        'license' => 'MIT',
        'keywords' => [
            'laravel',
            'alert',
            'notification',
            'toast',
            'modal',
            'inline',
            'blade',
            'javascript',
            'ajax',
            'websocket',
            'pusher',
            'email',
            'api',
            'rest',
            'real-time',
            'bootstrap',
            'tailwind',
            'bulma',
            'responsive',
            'accessible',
            'i18n',
            'customizable',
            'performance',
            'optimized',
            'testing',
            'documentation',
        ],
        'support' => [
            'email' => 'wahyudedik@gmail.com',
            'issues' => 'https://github.com/wahyudedik/LaravelAlert/issues',
            'source' => 'https://github.com/wahyudedik/LaravelAlert',
            'docs' => 'https://wahyudedik.github.io/LaravelAlert',
        ],
        'funding' => [
            'github' => 'https://github.com/sponsors/wahyudedik',
        ],
    ],
];
