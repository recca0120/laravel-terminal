<?php

namespace Recca0120\Terminal\Console\Commands;

use Exception;
use File;
use Illuminate\Console\Command;
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

    // {--P|P : Never follow symbolic links.  This is the default behaviour.  When find examines or prints information a file, and the file is a symbolic link, the information used shall be taken from the properties of the symbolic link itself.}
    // {--L|L : Follow  symbolic links.  When find examines or prints information about files, the information used shall be taken from the properties of the file to which the link points, not from the link itself (unless it is a broken symbolic link or find is unable to examine the file to which the link points).  Use of this  option  impliesnoleaf.   If  you  later  use  the  -P option, -noleaf will still be in effect.  If -L is in effect and find discovers a symbolic link to a subdirectory during its search, the subdirectory pointed to by the symbolic link will be searched. (unless the symbolic link is broken).  Using -L causes the -lname and -ilname predicates always to return false.}
    // {--H|H : Do  not follow symbolic links, except while processing the command line arguments.  When find examines or prints information about files, the information used shall be taken from the properties of the symbolic link itself.   The only exception to this behaviour is when a file specified on the command line is  a  symbolic  link, and  the link can be resolved.  For that situation, the information used is taken from whatever the link points to (that is, the link is followed).  The information about the link itself is used as a fallback if the file pointed to by the symbolic link cannot be examined.  If -H is in effect and one of the  paths  specified  on the command line is a symbolic link to a directory, the contents of that directory will be examined (though of course -maxdepth 0 would prevent this).}

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

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'search for files in a directory hierarchy';

    public function handle()
    {
        set_time_limit(0);
        $finder = new Finder();
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
                try {
                    if (File::isDirectory($realPath) === true) {
                        $deleted = File::deleteDirectory($realPath, true);
                    } else {
                        $deleted = File::delete($realPath);
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
