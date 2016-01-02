<?php

namespace Recca0120\Terminal\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Recca0120\Terminal\Console\Application as Artisan;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Artisan::class,
    ];

    /**
     * Get the Artisan application instance.
     *
     * @return \Illuminate\Console\Application
     */
    protected function getArtisan()
    {
        if (is_null($this->artisan)) {
            return $this->artisan = (new Artisan($this->app, $this->events, $this->app->version()))
                                ->resolveCommands($this->commands);
        }

        return $this->artisan;
    }

}

// use Illuminate\Foundation\Application;
// use Illuminate\Http\Request;

// class Kernel
// {
//     protected $app;

//     protected $request;

//     protected $resolveCommands = [];

//     /**
//      * The Artisan commands provided by your application.
//      *
//      * @var array
//      */
//     protected $commands = [
//         Commands\Artisan::class,
//         // Commands\Inspire::class,
//     ];

//     public function __construct(Application $app, Request $request)
//     {
//         $this->app = $app;
//         $this->request = $request;
//         foreach ($this->commands as $command) {
//             $cmd = $app->make($command);
//             $this->resolveCommands[$cmd->getSignature()] = $cmd;
//         }
//     }

//     public function handle()
//     {
//         $signature = array_get($this->request->get('cmd'), 'name');
//         if (isset($this->resolveCommands[$signature]) === true) {
//             $command = $this->resolveCommands[$signature];

//             return $command
//                 ->setApplication($this->app)
//                 ->setRequest($this->request)
//                 ->handle();
//         }
//     }
// }

