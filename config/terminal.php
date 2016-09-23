<?php

return [
    'enabled' => true,
    'whitelists' => [],
    'route' => [
        'prefix' => 'terminal',
        'as' => 'terminal.',
        'middleware' => ['web'],
    ],
    'commands' => [
        'Recca0120\Terminal\Console\Commands\Artisan',
        'Recca0120\Terminal\Console\Commands\ArtisanTinker',
        'Recca0120\Terminal\Console\Commands\Cleanup',
        'Recca0120\Terminal\Console\Commands\Find',
        'Recca0120\Terminal\Console\Commands\Mysql',
        'Recca0120\Terminal\Console\Commands\Tail',
        'Recca0120\Terminal\Console\Commands\Vi',
    ],
];
