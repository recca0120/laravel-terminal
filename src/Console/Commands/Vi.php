<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Vi extends Command
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
     * @var Filesystem
     */
    protected $files;

    /**
     * __construct.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Handle the command.
     *
     * @throws InvalidArgumentException
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $path = $this->argument('path');
        $text = $this->option('text');
        $root = function_exists('base_path') === true ? base_path() : getcwd();
        $path = rtrim($root, '/').'/'.$path;

        if ($text !== null) {
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
