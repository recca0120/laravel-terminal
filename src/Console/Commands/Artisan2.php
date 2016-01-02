<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Contracts\Console\Kernel as ArtisanContract;
use Recca0120\Terminal\Console\Command;

class Artisan extends Command
{
    protected $signature = 'artisan';

    protected $description = 'artisan';

    public function __construct(ArtisanContract $artisan)
    {
        $this->artisan = $artisan;
    }

    public function handle()
    {
        return $this->bufferedOutput(function ($bufferedOutput) {
            $argv = $this->argv;
            $firstArgument = $argv->getFirstArgument();
            if ((
                    starts_with($firstArgument, 'migrate') === true &&
                    starts_with($firstArgument, 'migrate:status') === false
                ) ||
                starts_with($firstArgument, 'db:seed') === true
            ) {
            }
            $argv->setOption('focus', true);
            dump($argv, get_class_methods($argv), $argv->getArguments());
            exit;
            // starts_with(method, "migrate") is true and starts_with(method, "migrate:status") is false) or starts_with(method, "db:seed")

            return $this->artisan->handle($this->argv, $bufferedOutput);
        });
    }
}
