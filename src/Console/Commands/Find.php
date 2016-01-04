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
        {--T|type= : -type alias -T}
        {--M|maxdepth= : -maxdepth alias -M}
        {--D|delete= : -delete alias -D}
    ';

    public function run(InputInterface $input, OutputInterface $output)
    {
        $cmd     = app('request')->get('cmd');
        $command = array_get($cmd, 'command');

        $str = strtr($command, [
            ' -name'     => ' -N',
            ' -type'     => ' -T',
            ' -maxdepth' => ' -M',
            ' -delete'   => ' -D true',
        ]);
        $input = new ArgvInput(array_merge(['php'], preg_split('/\s+/', $str)));

        return parent::run($input, $output);
    }

    /**
     * The console command description.
     *
     * @var string
     */

    // Usage: find [-H] [-L] [-P] [-Olevel] [-D help|tree|search|stat|rates|opt|exec] [path...] [expression]

    // default path is the current directory; default expression is -print
    // expression may consist of: operators, options, tests, and actions:

    // operators (decreasing precedence; -and is implicit where no others are given):
    //       ( EXPR )   ! EXPR   -not EXPR   EXPR1 -a EXPR2   EXPR1 -and EXPR2
    //       EXPR1 -o EXPR2   EXPR1 -or EXPR2   EXPR1 , EXPR2

    // positional options (always true): -daystart -follow -regextype

    // normal options (always true, specified before other expressions):
    //       -depth --help -maxdepth LEVELS -mindepth LEVELS -mount -noleaf
    //       --version -xdev -ignore_readdir_race -noignore_readdir_race

    // tests (N can be +N or -N or N): -amin N -anewer FILE -atime N -cmin N
    //       -cnewer FILE -ctime N -empty -false -fstype TYPE -gid N -group NAME
    //       -ilname PATTERN -iname PATTERN -inum N -iwholename PATTERN -iregex PATTERN
    //       -links N -lname PATTERN -mmin N -mtime N -name PATTERN -newer FILE
    //       -nouser -nogroup -path PATTERN -perm [-/]MODE -regex PATTERN
    //       -readable -writable -executable
    //       -wholename PATTERN -size N[bcwkMG] -true -type [bcdpflsD] -uid N
    //       -used N -user NAME -xtype [bcdpfls]
    //       -context CONTEXT

    // actions: -delete -print0 -printf FORMAT -fprintf FILE FORMAT -print
    //       -fprint0 FILE -fprint FILE -ls -fls FILE -prune -quit
    //       -exec COMMAND ; -exec COMMAND {} + -ok COMMAND ;
    //       -execdir COMMAND ; -execdir COMMAND {} + -okdir COMMAND ;

    // Report (and track progress on fixing) bugs via the findutils bug-reporting
    // page at http://savannah.gnu.org/ or, if you have no web access, by sending
    // email to <bug-findutils@gnu.org>.

    protected $description = 'search for files in a directory hierarchy';

    public function handle()
    {
        set_time_limit(0);
        $finder = new Finder;
        // dump($this->argument(), $this->option());

        $path     = $this->argument('path');
        $name     = $this->option('name');
        $type     = $this->option('type');
        $maxDepth = $this->option('maxdepth');
        $delete   = $this->option('delete');

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
