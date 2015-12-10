<?php

namespace Recca0120\Terminal\Console\Commands;

use File;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class Find extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find
        {path?}
        {--N|name= : -name alias -N}
        {--T|type= : -name alias -T}
        {--M|maxdepth= : -name alias -M}
        {--D|delete= : -name alias -D}
    ';

    public function run(InputInterface $input, OutputInterface $output)
    {
        $cmd = app('request')->get('cmd');
        $command = array_get($cmd, 'command');

        $str = strtr($command, [
            ' -name' => ' -N',
            ' -type' => ' -T',
            ' -maxdepth' => ' -M',
            ' -delete' => ' -D true',
        ]);
        $input = new ArgvInput(array_merge(['php'], preg_split('/\s+/', $str)));

        return parent::run($input, $output);
    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'find';

    public function handle()
    {
        set_time_limit(0);
        $finder = new Finder;
        // dump($this->argument(), $this->option());

        $path = $this->argument('path');
        $name = $this->option('name');
        $type = $this->option('type');
        $maxDepth = $this->option('maxdepth');
        $delete = $this->option('delete');

        if ($path === null) {
            $path = base_path();
        } else {
            $path = base_path($path);
        }

        $finder
            ->in($path);

        if ($name !== null) {
            $finder->name($name);
        }

        switch ($type) {
            case 'd':
                $finder->directories();
                break;
            case 'f':
                $finder->files();
                break;
        }

        if ($maxDepth !== null) {
            if ($maxDepth == '0') {
                $this->line($path);

                return;
            }
            $finder->depth('<'.$maxDepth);
        }

        foreach ($finder as $file) {
            $realPath = $file->getRealpath();
            if ($delete === 'true' && File::exists($realPath) === true) {
                if (File::isDirectory($realPath) === true) {
                    $deleted = File::deleteDirectory($realPath);
                } else {
                    $deleted = File::delete($realPath);
                }
                if ($deleted === true) {
                    $this->info($realPath.' deleted');
                } else {
                    $this->error($realPath.' isn\'t deleted');
                }
            } else {
                $this->line($file->getRealpath());
            }
        }
    }
}
