<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Mockery as m;
use MockingHelpers;
use Webmozart\Glob\Glob;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Illuminate\Filesystem\Filesystem;
use Recca0120\Terminal\Console\Commands\Cleanup;
use Symfony\Component\Console\Output\BufferedOutput;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;

class CleanupTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testFire()
    {
        $structure = [
            '.git' => [],
            '.svn' => [],
            'node_modules' => [],
            'test' => 'test',
            'vendor' => [
                'recca0120' => [
                    'terminal' => [
                        // others
                        '.git' => [],
                        '.svn' => [],
                        'node_modules' => [],
                        // docs
                        'readme' => 'readme',
                        'readme.md' => 'readme.md',
                        'README' => 'README',
                        'README.md' => 'README.md',
                        'CHANGELOG' => 'CHANGELOG',
                        'CHANGELOG.md' => 'CHANGELOG.md',
                        'FAQ' => 'FAQ',
                        'FAQ.md' => 'FAQ.md',
                        'CONTRIBUTING' => 'CONTRIBUTING',
                        'CONTRIBUTING.md' => 'CONTRIBUTING.md',
                        'HISTORY' => 'HISTORY',
                        'HISTORY.md' => 'HISTORY.md',
                        'UPGRADING' => 'UPGRADING',
                        'UPGRADING.md' => 'UPGRADING.md',
                        'UPGRADE' => 'UPGRADE',
                        'UPGRADE.md' => 'UPGRADE.md',
                        'package' => [],
                        'demo' => [],
                        'example' => [],
                        'doc' => [],
                        'docs' => [],
                        // tests
                        '.travis.yml' => '.travis.yml',
                        '.scrutinizer.yml' => '.scrutinizer.yml',
                        'phpunit.xml' => 'phpunit.xml',
                        'phpunit.xml.dist' => 'phpunit.xml.dist',
                        'phpunit.php' => 'phpunit.php',
                        'test' => [],
                        'tests' => [],
                        'Test' => [],
                        'Tests' => [],
                    ],
                ],
                'vendor' => [
                    'package' => [
                        // others
                        '.git' => [],
                        '.svn' => [],
                        'node_modules' => [],
                        // docs
                        'readme' => 'readme',
                        'readme.md' => 'readme.md',
                        'README' => 'README',
                        'README.md' => 'README.md',
                        'CHANGELOG' => 'CHANGELOG',
                        'CHANGELOG.md' => 'CHANGELOG.md',
                        'FAQ' => 'FAQ',
                        'FAQ.md' => 'FAQ.md',
                        'CONTRIBUTING' => 'CONTRIBUTING',
                        'CONTRIBUTING.md' => 'CONTRIBUTING.md',
                        'HISTORY' => 'HISTORY',
                        'HISTORY.md' => 'HISTORY.md',
                        'UPGRADING' => 'UPGRADING',
                        'UPGRADING.md' => 'UPGRADING.md',
                        'UPGRADE' => 'UPGRADE',
                        'UPGRADE.md' => 'UPGRADE.md',
                        'package' => [],
                        'demo' => [],
                        'example' => [],
                        'doc' => [],
                        'docs' => [],
                        // tests
                        '.travis.yml' => '.travis.yml',
                        '.scrutinizer.yml' => '.scrutinizer.yml',
                        'phpunit.xml' => 'phpunit.xml',
                        'phpunit.xml.dist' => 'phpunit.xml.dist',
                        'phpunit.php' => 'phpunit.php',
                        'test' => [],
                        'tests' => [],
                        'Test' => [],
                        'Tests' => [],
                    ],
                ],
            ],
        ];
        $root = vfsStream::setup('root', null, $structure);

        $command = new Cleanup(
            $filesystem = m::mock(new Filesystem)
        );
        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        MockingHelpers::mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        MockingHelpers::mockProperty($command, 'output', $output = new BufferedOutput);

        $laravel->shouldReceive('basePath')->once()->andReturn($basePath = $root->url());
        $filesystem->shouldReceive('glob')->andReturnUsing(function ($item) {
            return Glob::glob($item);
        });

        $command->fire();

        $this->assertSame([
            'root' => [
                'test' => 'test',
                'vendor' => [
                    'recca0120' => [
                        'terminal' => [],
                    ],
                    'vendor' => [
                        'package' => [],
                    ],
                ],
            ],
        ], vfsStream::inspect(new vfsStreamStructureVisitor())->getStructure());
    }
}
