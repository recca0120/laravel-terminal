<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
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
        $lines = $this->option('lines');

        if (empty($path) === false) {
            $path = $this->laravel->basePath().'/'.$path;
        } else {
            $path = $this->laravel->storagePath();
            $files = array_filter($this->filesystem->glob($path.'/logs/*.log'), function ($file) {
                return is_file($file);
            });
            usort($files, function ($a, $b) {
                $aTime = filectime($a);
                $bTime = filectime($b);
                if ($aTime == $bTime) {
                    return 0;
                }

                return ($aTime < $bTime) ? -1 : 1;
            });
            $path = $files[0];
        }

        $this->readLine($path, $lines);
    }

    /**
     * readLine.
     *
     * @method readLine
     *
     * @param string $file
     * @param int    $lines
     *
     * @return string
     */
    protected function readLine($file, $lines = 50)
    {
        if (file_exists($file) === false) {
            $this->error('tail: cannot open ‘'.$file.'’ for reading: No such file or directory');

            return;
        }

        $fp = fopen($file, 'r');
        $i = 1;
        $result = [];
        while (! feof($fp)) {
            if ($i > $lines) {
                break;
            }
            $content = fgets($fp);
            $result[] = $content;
            ++$i;
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
