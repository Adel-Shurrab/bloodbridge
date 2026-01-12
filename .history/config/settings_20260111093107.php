<?php

return [

    /*
     * Each settings class used in your application must be registered here.
     */
    'settings' => [
        App\Settings\GeneralSettings::class,
    ],

    /*
     * The path where the settings classes will be created.
     */
    'setting_class_path' => app_path('Settings'),

    /*
     * In these directories settings migrations will be stored and ran.
     */
    'migrations_paths' => [
        database_path('settings'),
    ],

    /*
     * The database connection to use.
     */
    'database_connection' => null,

    /*
     * The table name to use.
     */
    'table' => 'settings',

    /*
     * Cache configuration.
     */
    'cache' => [
        'enabled' => env('SETTINGS_CACHE_ENABLED', false),
    ],
];