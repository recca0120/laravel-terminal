<?php

namespace Recca0120\Terminal\Console;

use Recca0120\Terminal\Application as Artisan;
use Recca0120\Terminal\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Artisan::class,
        Commands\ArtisanTinker::class,
        Commands\Mysql::class,
        Commands\Find::class,
    ];
}
