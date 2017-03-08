<?php

namespace Recca0120\Terminal\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Find extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'find';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'search for files in a directory hierarchy';

    /**
     * $finder.
     *
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * $filesystem.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * __construct.
     *
     * @param \Symfony\Component\Finder\Finder  $finder
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Finder $finder, Filesystem $filesystem)
    {
        parent::__construct();
        $this->finder = $finder;
        $this->filesystem = $filesystem;
    }

    /**
     * run.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $command = (string) $input;
        $command = strtr($command, [
            ' -name' => ' -N',
            ' -type' => ' -T',
            ' -maxdepth' => ' -M',
            ' -delete' => ' -d true',
        ]);

        return parent::run(new StringInput($command), $output);
    }

    /**
     * fire.
     */
    public function fire()
    {
        $path = $this->argument('path');
        $name = $this->option('name');
        $type = $this->option('type');
        $maxDepth = $this->option('maxdepth');
        $delete = filter_var($this->option('delete'), FILTER_VALIDATE_BOOLEAN);

        $root = is_null($this->getLaravel()) === false ?
            $this->getLaravel()->basePath() : getcwd();
        $path = rtrim($root, '/').'/'.$path;

        $this->finder->in($path);

        if ($name !== null) {
            $this->finder->name($name);
        }

        switch ($type) {
            case 'd':
                $this->finder->directories();
                break;
            case 'f':
                $this->finder->files();
                break;
        }

        if (is_null($maxDepth) === false) {
            if ($maxDepth == '0') {
                $this->line($path);

                return;
            }
            $this->finder->depth('<'.$maxDepth);
        }

        foreach ($this->finder->getIterator() as $file) {
            $realPath = $file->getRealpath();
            if ($delete === true && $this->filesystem->exists($realPath) === true) {
                $removed = false;
                try {
                    if ($this->filesystem->isDirectory($realPath) === true) {
                        $removed = $this->filesystem->deleteDirectory($realPath);
                    } else {
                        $removed = $this->filesystem->delete($realPath);
                    }
                } catch (Exception $e) {
                    $removed = false;
                }
                $removed === true ? $this->info('removed '.$realPath) : $this->error('removed '.$realPath.' fail');
            } else {
                $this->line($realPath);
            }
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
            ['type', 'T', InputOption::VALUE_OPTIONAL, 'File is of type c: [f, d]'],
            ['name', 'N', InputOption::VALUE_OPTIONAL, 'Base of file name (the path with the leading directories removed) matches shell pattern pattern.  The metacharacters (`*\', `?\', and `[]\') match a `.\' at  the  start of  the  base name (this is a change in findutils-4.2.2; see section STANDARDS CONFORMANCE below).  To ignore a directory and the files under it, use -prune; see an example in the description of -path.  Braces are not recognised as being special, despite the fact that some shells including Bash imbue braces with a special meaning in shell patterns.  The filename matching is performed with the use of the fnmatch(3) library function.   Don\'t forget to enclose the pattern in quotes in order to protect it from expansion by the shell.'],
            ['maxdepth', 'M', InputOption::VALUE_OPTIONAL, '-maxdepth alias -M'],
            ['delete', 'd', InputOption::VALUE_OPTIONAL, 'Delete files; true if removal succeeded.  If the removal failed, an error message is issued.  If -delete fails, find\'s exit status will be nonzervagranto (when it  eventually exits).  Use of -delete automatically turns on the -depth option.'],
        ];
    }
}
