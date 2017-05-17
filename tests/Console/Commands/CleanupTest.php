<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Mockery as m;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Recca0120\Terminal\Console\Commands\Cleanup;
use Symfony\Component\Console\Output\BufferedOutput;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;

class CleanupTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
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
                'phpunit' => [
                    'phpunit' => [],
                ],
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
                        'vendor' => [],
                        '.editorconfig' => '.editorconfig',
                        '.nitpick.json' => '.nitpick.json',
                        '.php_cs' => '.php_cs',
                        'appveyor.yml' => 'appveyor.yml',
                        'ruleset.xml' => 'ruleset.xml',
                        'abc' => [
                            'tests' => [],
                        ],
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
                        '.babelrc' => '.babelrc',
                        '.travis.yml' => '.travis.yml',
                        '.scrutinizer.yml' => '.scrutinizer.yml',
                        'appveyor.yml' => 'appveyor.yml',
                        'phpunit.xml' => 'phpunit.xml',
                        'phpunit.xml.dist' => 'phpunit.xml.dist',
                        'phpunit.php' => 'phpunit.php',
                        'test' => [],
                        'tests' => [],
                        'Test' => [],
                        'Tests' => [],
                        'vendor' => [],
                    ],
                ],
            ],
        ];
        $root = vfsStream::setup('root', null, $structure);
        $container = m::mock(new Container);
        $container->shouldReceive('basePath')->andReturn($basePath = $root->url());
        Container::setInstance($container);

        $command = new Cleanup(
            $files = m::mock(new Filesystem)
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $command->fire();

        $this->assertSame([
            'root' => [
                'test' => 'test',
                'vendor' => [
                    'recca0120' => [
                        'terminal' => [
                            'abc' => [],
                        ],
                    ],
                    'vendor' => [
                        'package' => [],
                    ],
                ],
            ],
        ], vfsStream::inspect(new vfsStreamStructureVisitor())->getStructure());
    }

    protected function mockProperty($object, $propertyName, $value)
    {
        $reflectionClass = new \ReflectionClass($object);

        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }
}
