<?php

namespace Recca0120\Terminal\Console\Commands;

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
        $root = is_null($this->getLaravel()) === false ?
            $this->getLaravel()->basePath() : getcwd();
        $root = rtrim($root, '/').'/';

        $docs = ['README*', 'CHANGELOG*', 'FAQ*', 'CONTRIBUTING*', 'HISTORY*', 'UPGRADING*', 'UPGRADE*', 'package*', 'demo', 'example', 'examples', 'doc', 'docs', 'readme*'];
        $tests = ['.travis.yml', '.scrutinizer.yml', 'phpunit.xml*', 'phpunit.php', 'test', 'Test', 'tests', 'Tests', 'travis'];
        $others = ['.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg', 'node_modules'];

        (new Collection(array_merge($others, $docs, $tests)))
            ->map(function ($item) use ($root) {
                return $root.'vendor/*/*/'.$item;
            })
            ->merge(
                (new Collection($others))
                    ->map(function ($item) use ($root) {
                        return $root.$item;
                    })
            )
            ->map(function ($item) {
                return $this->filesystem->glob($item);
            })
            ->collapse()
            ->filter(function ($item) {
                return empty($item) === false;
            })
            ->each(function ($item) {
                if ($this->filesystem->isDirectory($item) === true) {
                    $this->filesystem->deleteDirectory($item);
                    $this->error('delete directory: '.$item);
                } else {
                    $this->filesystem->delete($item);
                    $this->error('delete file: '.$item);
                }
            });
    }
}
