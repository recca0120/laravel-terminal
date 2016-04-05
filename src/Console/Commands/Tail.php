<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Tail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tail
        {path?}
        {--lines=50 : output the last K lines, instead of the last 50}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'tail command';

    /**
     * handle.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function handle(Filesystem $filesystem)
    {
        $path = $this->argument('path');
        $lines = $this->option('lines');

        if (empty($path) === false) {
            $path = ((is_null($this->laravel) === false) ? $this->laravel->basePath() : getcwd()).'/'.$path;
        } else {
            $path = (is_null($this->laravel) === false) ? $this->laravel->storagePath() : getcwd();
            $path = collect($filesystem->glob($path.'/logs/*.log'))
                ->filter(function ($log) {
                    return is_file($log);
                })
                ->sortByDesc(function ($log) {
                    return filectime($log);
                })
                ->first();
        }

        $this->readLine($path, $lines);
    }

    protected function readLine($file, $lines = 50)
    {
        if (file_exists($file) === false) {
            return 'tail: cannot open ‘'.$file.'’ for reading: No such file or directory';
        }

        $fp = fopen($file, 'r');
        $i = 1;
        $result = [];
        while (!feof($fp)) {
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
}
