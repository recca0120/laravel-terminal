<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as ArtisanContract;
use Recca0120\Terminal\Console\CommandOnly;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\ArgvInput;

class Artisan extends Command
{
    use CommandOnly;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artisan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'artisan';

    protected $notSupport = [
        'down'   => '',
        'tinker' => '',
    ];

    public function __construct(ArtisanContract $artisan)
    {
        parent::__construct();
        $this->artisan = $artisan;
    }

    public function handle()
    {
        $argv    = $this->argv();
        $command = $this->command();
        if (isset($this->notSupport[$command]) === true) {
            throw new InvalidArgumentException('Command "'.$command.'" is not supported');
        }

        if ($this->needForce($command, $argv) === true) {
            array_push($argv, '--force');
        }
        $this->artisan->handle(new ArgvInput($argv), $this->getOutput());
    }

    protected function needForce($command, $argv)
    {
        return (
            (
                starts_with($command, 'migrate') === true && starts_with($command, 'migrate:status') === false
            ) ||
            starts_with($command, 'db:seed') === true
        ) && in_array('--force', $argv) === false;
    }
}
