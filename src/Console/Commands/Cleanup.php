<?php

namespace Recca0120\Terminal\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Cleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cleanup vendor folder';

    /**
     * $filesystem.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * handle.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function handle(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $root = $this->laravel->basePath();

        $this->deleteDirectory([
            $root.'/node_modules',
            $root.'/vendor/*/*/node_modules',
            $root.'/vendor/*/*/vendor',
            $root.'/vendor/*/*/.git',
        ]);

        $this->cleanupVendor(realpath($root.'/vendor'));
    }

    /**
     * deleteDirectory.
     *
     * @method deleteDirectory
     *
     * @param array $directories
     */
    public function deleteDirectory($directories)
    {
        foreach ($directories as $directory) {
            $files = $this->filesystem->glob($directory, GLOB_ONLYDIR);
            $this->delete($files);
            if ($this->filesystem->isDirectory($directory) === true) {
                @rmdir($directory);
            }
        }
    }

    /**
     * cleanupVendor.
     *
     * @method cleanupVendor
     *
     * @param string $vendorDirectory
     */
    public function cleanupVendor($vendorDirectory)
    {
        $rules = static::getRules();
        foreach ($rules as $packageDir => $rules) {
            $packageDir = $vendorDirectory.'/'.$packageDir;
            if ($this->filesystem->isDirectory($packageDir) === false) {
                continue;
            }

            foreach ((array) $rules as $part) {
                $patterns = explode(' ', trim($part));
                foreach ($patterns as $pattern) {
                    $this->delete($this->filesystem->glob($packageDir.'/'.$pattern));
                }
            }
        }
    }

    /**
     * delete.
     *
     * @method delete
     *
     * @param array $files
     */
    public function delete($files)
    {
        if (count($files) === 0) {
            return;
        }
        $directories = [];
        foreach ($files as $file) {
            try {
                if ($this->filesystem->isDirectory($file) === true) {
                    $this->filesystem->deleteDirectory($file, true);
                } else {
                    $this->filesystem->delete($file);
                }
                $this->info(sprintf('delete: %s', $file));
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }

    /**
     * getRules.
     *
     * @method getRules
     *
     * @return array
     */
    public static function getRules()
    {
        // Default patterns for common files
        $docs = 'README* CHANGELOG* FAQ* CONTRIBUTING* HISTORY* UPGRADING* UPGRADE* package* demo example examples doc docs readme*';
        $tests = '.travis.yml .scrutinizer.yml phpunit.xml* phpunit.php test tests Tests travis';

        return [
           // Symfony components
           'symfony/browser-kit'                   => [$docs, $tests],
           'symfony/class-loader'                  => [$docs, $tests],
           'symfony/console'                       => [$docs, $tests],
           'symfony/css-selector'                  => [$docs, $tests],
           'symfony/debug'                         => [$docs, $tests],
           'symfony/dom-crawler'                   => [$docs, $tests],
           'symfony/event-dispatcher'              => [$docs, $tests],
           'symfony/filesystem'                    => [$docs, $tests],
           'symfony/finder'                        => [$docs, $tests],
           'symfony/http-foundation'               => [$docs, $tests],
           'symfony/http-kernel'                   => [$docs, $tests],
           'symfony/process'                       => [$docs, $tests],
           'symfony/routing'                       => [$docs, $tests],
           'symfony/security'                      => [$docs, $tests],
           'symfony/security-core'                 => [$docs, $tests],
           'symfony/translation'                   => [$docs, $tests],
           'symfony/var-dumper'                    => [$docs, $tests],
           // Default Laravel 4 install
           'classpreloader/classpreloader'         => [$docs, $tests],
           'd11wtq/boris'                          => [$docs, $tests],
           'filp/whoops'                           => [$docs, $tests],
           'ircmaxell/password-compat'             => [$docs, $tests],
           'jeremeamia/SuperClosure'               => [$docs, $tests, 'demo'],
           'laravel/framework'                     => [$docs, $tests, 'build'],
           'monolog/monolog'                       => [$docs, $tests],
           'nesbot/carbon'                         => [$docs, $tests],
           'nikic/php-parser'                      => [$docs, $tests, 'test_old'],
           'patchwork/utf8'                        => [$docs, $tests],
           'phpseclib/phpseclib'                   => [$docs, $tests, 'build'],
           'predis/predis'                         => [$docs, $tests, 'bin'],
           'psr/log'                               => [$docs, $tests],
           'stack/builder'                         => [$docs, $tests],
           'swiftmailer/swiftmailer'               => [$docs, $tests, 'build* notes test-suite create_pear_package.php'],
           // Common packages
           'anahkiasen/former'                     => [$docs, $tests],
           'anahkiasen/html-object'                => [$docs, 'phpunit.xml* tests/*'],
           'anahkiasen/underscore-php'             => [$docs, $tests],
           'anahkiasen/rocketeer'                  => [$docs, $tests],
           'barryvdh/composer-cleanup-plugin'      => [$docs, $tests],
           'barryvdh/laravel-debugbar'             => [$docs, $tests],
           'barryvdh/laravel-ide-helper'           => [$docs, $tests],
           'bllim/datatables'                      => [$docs, $tests],
           'cartalyst/sentry'                      => [$docs, $tests],
           'dflydev/markdown'                      => [$docs, $tests],
           'doctrine/annotations'                  => [$docs, $tests, 'bin'],
           'doctrine/cache'                        => [$docs, $tests, 'bin'],
           'doctrine/collections'                  => [$docs, $tests],
           'doctrine/common'                       => [$docs, $tests, 'bin lib/vendor'],
           'doctrine/dbal'                         => [$docs, $tests, 'bin build* docs2 lib/vendor'],
           'doctrine/inflector'                    => [$docs, $tests],
           'dompdf/dompdf'                         => [$docs, $tests, 'www'],
           'guzzle/guzzle'                         => [$docs, $tests],
           'guzzlehttp/guzzle'                     => [$docs, $tests],
           'guzzlehttp/oauth-subscriber'           => [$docs, $tests],
           'guzzlehttp/streams'                    => [$docs, $tests],
           'imagine/imagine'                       => [$docs, $tests, 'lib/Imagine/Test'],
           'intervention/image'                    => [$docs, $tests, 'public'],
           'jasonlewis/basset'                     => [$docs, $tests],
           'kriswallsmith/assetic'                 => [$docs, $tests],
           'leafo/lessphp'                         => [$docs, $tests, 'Makefile package.sh'],
           'league/stack-robots'                   => [$docs, $tests],
           'maximebf/debugbar'                     => [$docs, $tests, 'demo'],
           'mccool/laravel-auto-presenter'         => [$docs, $tests],
           'mockery/mockery'                       => [$docs, $tests],
           'mrclay/minify'                         => [$docs, $tests, 'MIN.txt min_extras min_unit_tests min/builder min/config* min/quick-test* min/utils.php min/groupsConfig.php min/index.php'],
           'mustache/mustache'                     => [$docs, $tests, 'bin'],
           'oyejorge/less.php'                     => [$docs, $tests],
           'phenx/php-font-lib'                    => [$docs, $tests.'www'],
           'phpdocumentor/reflection-docblock'     => [$docs, $tests],
           'phpoffice/phpexcel'                    => [$docs, $tests, 'Examples unitTests changelog.txt'],
           'rcrowe/twigbridge'                     => [$docs, $tests],
           'simplepie/simplepie'                   => [$docs, $tests, 'build compatibility_test ROADMAP.md'],
           'tijsverkoyen/css-to-inline-styles'     => [$docs, $tests],
           'twig/twig'                             => [$docs, $tests],
           'venturecraft/revisionable'             => [$docs, $tests],
           'willdurand/geocoder'                   => [$docs, $tests],
       ];
    }
}
