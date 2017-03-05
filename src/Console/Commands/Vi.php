<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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
     * $filesystem.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * __construct.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * fire.
     */
    public function fire()
    {
        $path = $this->argument('path');
        $text = $this->option('text');
        $root = is_null($this->getLaravel()) === false ?
            $this->getLaravel()->basePath() : getcwd();
        $path = trim($root, '/').'/'.$path;

        if (is_null($text) === false) {
            $this->filesystem->put($path, $text);
        } else {
            $this->line($this->filesystem->get($path));
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
