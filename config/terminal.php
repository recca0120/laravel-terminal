<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Package Enabled
    |--------------------------------------------------------------------------
    |
    | This value determines whether the package is enabled. By default it
    | will be enabled if APP_DEBUG is true.
    |
    */
    'enabled' => env('APP_DEBUG'),

    /*
    |--------------------------------------------------------------------------
    | Whitelisted IP Addresses
    |--------------------------------------------------------------------------
    |
    | This value contains a list of IP addresses that are allowed to access
    | the Laravel terminal.
    |
    */

    'whitelists' => [],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | This value sets the route information such as the prefix and middleware.
    |
    */

    'route' => [
        'prefix' => 'terminal',
        'as' => 'terminal.',
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Enabled Commands
    |--------------------------------------------------------------------------
    |
    | This value contains a list of class names for the available commands
    | for Laravel Terminal.
    |
    */

    'commands' => [
        \Recca0120\Terminal\Console\Commands\Artisan::class,
        \Recca0120\Terminal\Console\Commands\ArtisanTinker::class,
        \Recca0120\Terminal\Console\Commands\Cleanup::class,
        \Recca0120\Terminal\Console\Commands\Composer::class,
        \Recca0120\Terminal\Console\Commands\Find::class,
        \Recca0120\Terminal\Console\Commands\Mysql::class,
        \Recca0120\Terminal\Console\Commands\Tail::class,
        \Recca0120\Terminal\Console\Commands\Vi::class,
    ],
];
