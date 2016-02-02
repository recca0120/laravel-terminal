<?php

namespace Recca0120\Terminal;

use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Recca0120\Terminal\Application as Artisan;

class Kernel implements KernelContract
{
    /**
     * The Artisan application instance.
     *
     * @var \Illuminate\Console\Application
     */
    protected $artisan;

    /**
     * The Artisan commands provided by the application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Create a new console kernel instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function __construct(ApplicationContract $app, Dispatcher $events)
    {
        $this->app = $app;
        $this->events = $events;
    }

    /**
     * Handle an incoming console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    public function handle($input, $output = null)
    {
        return $this->getArtisan()->run($input, $output);
    }

    /**
     * Run an Artisan console command by name.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return int
     */
    public function call($command, array $parameters = [])
    {
        return $this->getArtisan()->call($command, $parameters);
    }

    /**
     * Queue an Artisan console command by name.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return int
     */
    public function queue($command, array $parameters = [])
    {
    }

    /**
     * Get all of the commands registered with the console.
     *
     * @return array
     */
    public function all()
    {
        return $this->getArtisan()->all();
    }

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output()
    {
        return $this->getArtisan()->output();
    }

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
