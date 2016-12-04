<?php

return [
    'enabled' => env('APP_DEBUG') === true,
    'whitelists' => [],
    'route' => [
        'prefix' => 'terminal',
        'as' => 'terminal.',
        'middleware' => ['web'],
    ],
    'interpreters' => [
        'mysql' => 'mysql',
        'artisan tinker' => 'tinker',
        'tinker' => 'tinker',
    ],
    'confirmToProceed' => [
        'artisan' => [
            'migrate',
            'migrate:install',
            'migrate:refresh',
            'migrate:reset',
            'migrate:rollback',
            'db:seed',
        ],
    ],
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
