<?php

namespace Recca0120\Terminal\Console\Commands;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Illuminate\Contracts\Console\Kernel as ArtisanContract;

class Artisan extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'artisan';

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
     * $artisan.
     *
     * @var \Illuminate\Contracts\Console\Kernel
     */
    protected $artisan;

    /**
     * __construct.
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
     * @throws \InvalidArgumentException
     */
    public function fire()
    {
        $command = $this->forceCommand($this->option('command'));

        $input = new StringInput($command);
        $input->setInteractive(false);
        if (isset($this->notSupport[$input->getFirstArgument()]) === true) {
            throw new InvalidArgumentException('Command "'.$command.'" is not supported');
        }
        $this->artisan->handle($input, $this->getOutput());
    }

    /**
     * need focre option.
     *
     * @param string $command
     * @return string
     */
    protected function forceCommand($command)
    {
        return (
            starts_with($command, 'migrate') === true && starts_with($command, 'migrate:status') === false ||
            starts_with($command, 'db:seed') === true
        ) && strpos('command', '--force') === false ?
            $command .= ' --force' : $command;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['command', null, InputOption::VALUE_OPTIONAL],
        ];
    }
}
