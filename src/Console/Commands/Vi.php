<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Recca0120\Terminal\Contracts\WebCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Vi extends Command implements WebCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'vi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vi Editor';

    /**
     * $files.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * __construct.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Handle the command.
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        $path = $this->argument('path');
        $text = $this->option('text');
        $root = function_exists('base_path') === true ? base_path() : getcwd();
        $path = trim($root, '/').'/'.$path;

        if (is_null($text) === false) {
            $this->files->put($path, $text);
        } else {
            $this->line($this->files->get($path));
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['path', InputArgument::REQUIRED, 'path'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['text', null, InputOption::VALUE_OPTIONAL],
        ];
    }
}
