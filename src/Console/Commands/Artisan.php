<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Contracts\Console\Kernel as ArtisanContract;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;

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
    protected $notSupported = ['down' => '', 'tinker' => ''];

    /**
     * $artisan.
     *
     * @var ArtisanContract
     */
    protected $artisan;

    /**
     * __construct.
     *
     * @param ArtisanContract $artisan
     */
    public function __construct(ArtisanContract $artisan)
    {
        parent::__construct();

        $this->artisan = $artisan;
    }

    /**
     * Handle the command.
     *
     * @throws InvalidArgumentException
     */
    public function handle()
    {
        $command = $this->fixCommand(trim($this->option('command')));

        $input = new StringInput($command);
        $input->setInteractive(false);
        if (isset($this->notSupported[$input->getFirstArgument()]) === true) {
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
    protected function fixCommand($command)
    {
        $isMigrateCommand = Str::startsWith($command, 'migrate') === true && Str::startsWith($command, 'migrate:status') === false;
        if (($isMigrateCommand || Str::startsWith($command, 'db:seed') === true) && strpos($command, '--force') === false) {
            $command .= ' --force';
        }

        $isLaravel55 = $this->laravel !== null && version_compare($this->laravel->version(), 5.5, '>=');
        if (($isLaravel55 && Str::startsWith($command, 'vendor:publish') === true) && strpos($command, '--all') === false) {
            $command .= ' --all';
        }

        return $command;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['command', null, InputOption::VALUE_REQUIRED],
        ];
    }
}
