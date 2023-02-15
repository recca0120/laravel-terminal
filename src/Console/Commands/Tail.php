<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Tail extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'tail command';

    /**
     * $files.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * __construct.
     *
     * @param  Filesystem  $files
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
        $lines = (int) $this->option('lines');

        if (empty($path) === false) {
            $root = function_exists('base_path') === true ? base_path() : getcwd();
            $file = rtrim($root, '/').'/'.$path;
        } else {
            $path = function_exists('storage_path') === true ? storage_path() : getcwd();
            $path = rtrim($path, '/').'/';

            $file = (new Collection($this->files->glob($path.'logs/*.log')))
                ->map(function ($file) {
                    return is_file($file) === true ? $file : false;
                })->sortByDesc(function ($file) {
                    return filectime($file);
                })->first();
        }

        $this->readLine($file, $lines);
    }

    /**
     * readLine.
     *
     * @param  string  $file
     * @param  int  $lines
     */
    protected function readLine($file, $lines = 50)
    {
        if (is_file($file) === false) {
            $this->error('tail: cannot open ‘'.$file.'’ for reading: No such file or directory');

            return;
        }

        $fp = fopen($file, 'rb');
        $i = 1;
        $result = [];
        while (! feof($fp)) {
            if ($i > $lines) {
                break;
            }
            $content = fgets($fp);
            $result[] = $content;
            $i++;
        }
        fclose($fp);

        $this->line(implode('', $result));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['path', InputArgument::OPTIONAL, 'path'],
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
            ['lines', null, InputOption::VALUE_OPTIONAL, 'output the last K lines, instead of the last 50', 50],
        ];
    }
}
