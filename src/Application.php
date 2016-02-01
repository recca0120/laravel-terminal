<?php

namespace Recca0120\Terminal;

use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;

class Application extends ConsoleApplication
{
    /**
     * Create a new Artisan console application.
     *
     * @param  \Illuminate\Contracts\Container\Container  $laravel
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @param  string  $version
     * @return void
     */
    public function __construct(Container $laravel, Dispatcher $events)
    {
        parent::__construct($laravel, $events, $laravel->version());
    }

    /**
     * only register custom command.
     *
     * @param string $commands
     * @param bool   $customCommand
     *
     * @return $this
     */
    public function resolveCommands($commands, $loadCommand = false)
    {
        if ($loadCommand === false) {
            return $this;
        }

        return parent::resolveCommands($commands);
    }
}
