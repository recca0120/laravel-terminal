<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as ArtisanContract;
use InvalidArgumentException;
use Symfony\Component\Console\Input\StringInput;

class Artisan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'artisan {--command=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'laravel artisan';

    /**
     * no support array.
     *
     * @var array
     */
    protected $notSupport = [
        'down' => '',
        'tinker' => '',
    ];

    /**
     * handle.
     *
     * @param \Illuminate\Contracts\Console\Kernel $artisan
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function handle(ArtisanContract $artisan)
    {
        $command = $this->option('command');

        if ($this->needForce($command) === true) {
            $command .= ' --force';
        }
        $input = new StringInput($command);
        $input->setInteractive(false);
        if (isset($this->notSupport[$input->getFirstArgument()]) === true) {
            throw new InvalidArgumentException('Command "'.$command.'" is not supported');
        }
        $artisan->handle($input, $this->getOutput());
    }

    /**
     * need focre option.
     *
     * @param string $command
     *
     * @return bool
     */
    protected function needForce($command)
    {
        return (
            (
                starts_with($command, 'migrate') === true && starts_with($command, 'migrate:status') === false
            ) ||
            starts_with($command, 'db:seed') === true
        ) && strpos('command', '--force') === false;
    }
}
