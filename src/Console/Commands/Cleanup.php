<?php

namespace Recca0120\Terminal\Console\Commands;

use Webmozart\Glob\Glob;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;


class Cleanup extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cleanup';

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
     *
     * @method fire
     */
    public function fire()
    {
        set_time_limit(0);
        $root = is_null($this->getLaravel()) === false ?
            $this->getLaravel()->basePath() : getcwd();
        $root = rtrim($root, '/').'/';

        $docs = ['README*', 'CHANGELOG*', 'FAQ*', 'CONTRIBUTING*', 'HISTORY*', 'UPGRADING*', 'UPGRADE*', 'package*', 'demo', 'example', 'examples', 'doc', 'docs', 'readme*'];
        $tests = ['.travis.yml', '.scrutinizer.yml', 'phpunit.xml*', 'phpunit.php', 'test', 'Test', 'tests', 'Tests', 'travis'];
        $others = ['vendor'];
        $common = ['.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg', 'node_modules'];

        (new Collection(Glob::glob($root.'vendor/*/*')))
            ->map(function ($path) use ($common, $others, $docs, $tests) {
                return (new Collection(array_merge($common, $others, $docs, $tests)))
                    ->map(function($item) use ($path) {
                        return rtrim($path, '/').'/**/'.$item;
                    });
            })
            ->collapse()
            ->unique()
            ->filter()
            ->merge(
                (new Collection($common))
                    ->map(function ($item) use ($root) {
                        return $root.$item;
                    })
            )
            ->each(function ($item) {
                (new Collection(Glob::glob($item)))
                    ->unique()
                    ->filter()
                    ->each(function ($item) {
                        if ($this->filesystem->isDirectory($item) === true) {
                            $this->filesystem->deleteDirectory($item);
                            $this->error('delete directory: '.$item);
                        } else {
                            $this->filesystem->delete($item);
                            $this->error('delete file: '.$item);
                        }
                    });
            });
    }
}
