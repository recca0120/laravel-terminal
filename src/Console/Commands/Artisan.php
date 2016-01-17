<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as ArtisanContract;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\StringInput;

class Artisan extends Command
{
    use Traits\CommandOnly;
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

    /**
     * no support array.
     *
     * @var array
     */
    protected $notSupport = [
        'down'   => '',
        'tinker' => '',
    ];

    /**
     * construct.
     *
     * @param \Illuminate\Contracts\Console\Kernel $artisan
     */
    public function __construct(ArtisanContract $artisan)
    {
        parent::__construct();
        $this->artisan = $artisan;
    }

    /**
     * handle.
     *
     * @param  \Illuminate\Database\Connection $connection
     * @return void
     */
    public function handle()
    {
        $command = $this->argument('command');
        if ($this->needForce($command) === true) {
            $command .= ' --force';
        }
        $input = new StringInput($command);

        if (isset($this->notSupport[$input->getFirstArgument()]) === true) {
            throw new InvalidArgumentException('Command "'.$command.'" is not supported');
        }
        $this->artisan->handle($input, $this->getOutput());
    }

    /**
     * need focre option.
     *
     * @param  string $command
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
