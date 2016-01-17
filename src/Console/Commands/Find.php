<?php

namespace Recca0120\Terminal\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
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
        {--T|type= : File is of type c: [f, d]}
        {--N|name= : Base of file name (the path with the leading directories removed) matches shell pattern pattern.  The metacharacters (`*\', `?\', and `[]\') match a `.\' at  the  start of  the  base name (this is a change in findutils-4.2.2; see section STANDARDS CONFORMANCE below).  To ignore a directory and the files under it, use -prune; see an example in the description of -path.  Braces are not recognised as being special, despite the fact that some shells including Bash imbue braces with a special meaning in shell patterns.  The filename matching is performed with the use of the fnmatch(3) library function.   Don\'t forget to enclose the pattern in quotes in order to protect it from expansion by the shell.}
        {--M|maxdepth= : -maxdepth alias -M}
        {--d|delete= : Delete files; true if removal succeeded.  If the removal failed, an error message is issued.  If -delete fails, find\'s exit status will be nonzervagranto (when it  eventually exits).  Use of -delete automatically turns on the -depth option.}
    ';

    /**
     * [run description].
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Symfony\Component\Console\Input\StringInput
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $cmd = app('request')->get('cmd');
        $command = array_get($cmd, 'command');
        $command = strtr($command, [
            ' -name'     => ' -N',
            ' -type'     => ' -T',
            ' -maxdepth' => ' -M',
            ' -delete'   => ' -d true',
        ]);
        $input = new StringInput($command);

        return parent::run($input, $output);
    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'search for files in a directory hierarchy';

    /**
     * handle.
     *
     * @param  \Illuminate\Database\Connection $connection
     * @return void
     */
    public function handle(Filesystem $filesystem)
    {
        // set_time_limit(30);
        $finder = new Finder();

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
            if ($delete === 'true' && $filesystem->exists($realPath) === true) {
                try {
                    if ($filesystem->isDirectory($realPath) === true) {
                        $deleted = $filesystem->deleteDirectory($realPath, true);
                    } else {
                        $deleted = $filesystem->delete($realPath);
                    }
                } catch (Exception $e) {
                }
                if ($deleted === true) {
                    $this->info('removed '.$realPath);
                } else {
                    $this->error('removed '.$realPath.' fail');
                }
            } else {
                $this->line($file->getRealpath());
            }
        }
    }
}
