<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Phar;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;

class Composer extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'composer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'composer';

    /**
     * $application.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $application;

    /**
     * $filesystem.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * $composerPath.
     *
     * @var string
     */
    protected $composerPath;

    /**
     * __construct.
     *
     * @param \Illuminate\Contracts\Foundation\Application
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Application $application, Filesystem $filesystem)
    {
        parent::__construct();
        $this->application = $application;
        $this->filesystem = $filesystem;
        $storagePath = $this->application->storagePath();
        $this->composerPath = $storagePath.'/composer';
    }

    /**
     * install.
     *
     * @method install
     */
    protected function install()
    {
        if ($this->filesystem->exists($this->composerPath.'/vendor/autoload.php') === false) {
            if ($this->filesystem->isDirectory($this->composerPath) === false) {
                $this->filesystem->makeDirectory($this->composerPath, 0777);
            }
            $this->filesystem->put($this->composerPath.'/composer.phar', file_get_contents('https://getcomposer.org/composer.phar'));
            $composerPhar = new Phar($this->composerPath.'/composer.phar');
            $composerPhar->extractTo($this->composerPath);
            unset($composerPhar);
            $this->filesystem->delete($this->composerPath.'/composer.phar');
        }
        putenv('COMPOSER_HOME='.$this->composerPath);
        $this->filesystem->getRequire($this->composerPath.'/vendor/autoload.php');
        $this->init();
    }

    /**
     * init.
     *
     * @method init
     */
    protected function init()
    {
        error_reporting(-1);

        // $xdebug = new \Composer\XdebugHandler($this->getOutput());
        // $xdebug->check();
        // unset($xdebug);
        if (function_exists('ini_set')) {
            @ini_set('display_errors', 1);
            $memoryInBytes = function ($value) {
                $unit = strtolower(substr($value, -1, 1));
                $value = (int) $value;
                switch ($unit) {
                    case 'g':
                        $value *= 1024;
                        // no break (cumulative multiplier)
                    case 'm':
                        $value *= 1024;
                        // no break (cumulative multiplier)
                    case 'k':
                        $value *= 1024;
                }

                return $value;
            };
            $memoryLimit = trim(ini_get('memory_limit'));
            // Increase memory_limit if it is lower than 1GB
            if ($memoryLimit != -1 && $memoryInBytes($memoryLimit) < 1024 * 1024 * 1024) {
                @ini_set('memory_limit', '1G');
            }
            unset($memoryInBytes, $memoryLimit);
        }
    }

    /**
     * handle.
     */
    public function fire()
    {
        chdir($this->application->basePath());
        $command = trim($this->option('command'));
        if (empty($command) === true) {
            $command = 'help';
        }
        $input = new StringInput($command);

        $this->install();
        $application = new \Composer\Console\Application();
        $application->setAutoExit(false);
        $application->run($input, $this->getOutput());
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['command', null, InputOption::VALUE_OPTIONAL],
        ];
    }
}
