<?php

namespace Recca0120\Terminal\Tests\Console\Commands;

use Exception;
use Mockery as m;
use Webmozart\Glob\Glob;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Recca0120\Terminal\Console\Commands\Find;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FindTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $structure = [
            'foo' => [
                'foo' => 'foo',
                'bar' => [],
            ],
        ];
        $this->root = vfsStream::setup('root', null, $structure);
        $container = m::mock(new Container);
        $container->shouldReceive('basePath')->andReturn($this->root->url());
        Container::setInstance($container);
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testHandleFindName()
    {
        $command = new Find(
            $finder = m::mock('Symfony\Component\Finder\Finder'),
            $files = m::mock(new Filesystem)
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getArgument')->once()->with('path')->andReturn($path = 'foo');
        $input->shouldReceive('getOption')->once()->with('name')->andReturn($name = 'foo');
        $input->shouldReceive('getOption')->once()->with('type')->andReturn(null);
        $input->shouldReceive('getOption')->once()->with('maxdepth')->andReturn(null);
        $input->shouldReceive('getOption')->once()->with('delete')->andReturn(null);

        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $basePath = $this->root->url();

        $finder->shouldReceive('in')->once()->with($basePath.'/'.$path);
        $finder->shouldReceive('name')->once()->with($name);
        $finder->shouldReceive('getIterator')->once()->andReturnUsing(function () use ($basePath) {
            return array_map(function ($file) {
                $fileinfo = m::mock('SplFileInfo');
                $fileinfo->shouldReceive('getRealPath')->andReturn($file);

                return $fileinfo;
            }, Glob::glob($basePath.'/*'));
        });
        $this->assertNull($command->handle());
    }

    public function testHandleFindNameByDirectory()
    {
        $command = new Find(
            $finder = m::mock('Symfony\Component\Finder\Finder'),
            $files = m::mock(new Filesystem)
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getArgument')->once()->with('path')->andReturn($path = 'foo');
        $input->shouldReceive('getOption')->once()->with('name')->andReturn($name = 'foo');
        $input->shouldReceive('getOption')->once()->with('type')->andReturn($type = 'd');
        $input->shouldReceive('getOption')->once()->with('maxdepth')->andReturn(null);
        $input->shouldReceive('getOption')->once()->with('delete')->andReturn(null);

        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $basePath = $this->root->url();

        $finder->shouldReceive('in')->once()->with($basePath.'/'.$path);
        $finder->shouldReceive('name')->once()->with($name);
        $finder->shouldReceive('directories')->once();
        $finder->shouldReceive('getIterator')->once()->andReturnUsing(function () use ($basePath) {
            return array_map(function ($file) {
                $fileinfo = m::mock('SplFileInfo');
                $fileinfo->shouldReceive('getRealPath')->andReturn($file);

                return $fileinfo;
            }, Glob::glob($basePath.'/*'));
        });
        $this->assertNull($command->handle());
    }

    public function testHandleFindNameByFile()
    {
        $command = new Find(
            $finder = m::mock('Symfony\Component\Finder\Finder'),
            $files = m::mock(new Filesystem)
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getArgument')->once()->with('path')->andReturn($path = 'foo');
        $input->shouldReceive('getOption')->once()->with('name')->andReturn($name = 'foo');
        $input->shouldReceive('getOption')->once()->with('type')->andReturn($type = 'f');
        $input->shouldReceive('getOption')->once()->with('maxdepth')->andReturn(null);
        $input->shouldReceive('getOption')->once()->with('delete')->andReturn(null);

        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $basePath = $this->root->url();

        $finder->shouldReceive('in')->once()->with($basePath.'/'.$path);
        $finder->shouldReceive('name')->once()->with($name);
        $finder->shouldReceive('files')->once();
        $finder->shouldReceive('getIterator')->once()->andReturnUsing(function () use ($basePath) {
            return array_map(function ($file) {
                $fileinfo = m::mock('SplFileInfo');
                $fileinfo->shouldReceive('getRealPath')->andReturn($file);

                return $fileinfo;
            }, Glob::glob($basePath.'/*'));
        });
        $this->assertNull($command->handle());
    }

    public function testHandleFindMaxDepthIsZero()
    {
        $command = new Find(
            $finder = m::mock('Symfony\Component\Finder\Finder'),
            $files = m::mock(new Filesystem)
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getArgument')->once()->with('path')->andReturn($path = 'foo');
        $input->shouldReceive('getOption')->once()->with('name')->andReturn($name = 'foo');
        $input->shouldReceive('getOption')->once()->with('type')->andReturn(null);
        $input->shouldReceive('getOption')->once()->with('maxdepth')->andReturn('0');
        $input->shouldReceive('getOption')->once()->with('delete')->andReturn(null);

        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $basePath = $this->root->url();

        $finder->shouldReceive('in')->once()->with($basePath.'/'.$path);
        $finder->shouldReceive('name')->once()->with($name);

        $this->assertNull($command->handle());
    }

    public function testHandleFindMaxDepthBiggerZero()
    {
        $command = new Find(
            $finder = m::mock('Symfony\Component\Finder\Finder'),
            $files = m::mock(new Filesystem)
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getArgument')->once()->with('path')->andReturn($path = 'foo');
        $input->shouldReceive('getOption')->once()->with('name')->andReturn($name = 'foo');
        $input->shouldReceive('getOption')->once()->with('type')->andReturn(null);
        $input->shouldReceive('getOption')->once()->with('maxdepth')->andReturn('1');
        $input->shouldReceive('getOption')->once()->with('delete')->andReturn(null);

        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $basePath = $this->root->url();

        $finder->shouldReceive('in')->once()->with($basePath.'/'.$path);
        $finder->shouldReceive('name')->once()->with($name);
        $finder->shouldReceive('depth')->once()->with('<1');
        $finder->shouldReceive('getIterator')->once()->andReturnUsing(function () use ($basePath) {
            return array_map(function ($file) {
                $fileinfo = m::mock('SplFileInfo');
                $fileinfo->shouldReceive('getRealPath')->andReturn($file);

                return $fileinfo;
            }, Glob::glob($basePath.'/*'));
        });
        $this->assertNull($command->handle());
    }

    public function testHandleDelete()
    {
        $command = new Find(
            $finder = m::mock('Symfony\Component\Finder\Finder'),
            $files = m::mock(new Filesystem)
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getArgument')->once()->with('path')->andReturn($path = 'foo');
        $input->shouldReceive('getOption')->once()->with('name')->andReturn($name = 'foo');
        $input->shouldReceive('getOption')->once()->with('type')->andReturn(null);
        $input->shouldReceive('getOption')->once()->with('maxdepth')->andReturn(null);
        $input->shouldReceive('getOption')->once()->with('delete')->andReturn($delete = 'true');

        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $basePath = $this->root->url();

        $finder->shouldReceive('in')->once()->with($basePath.'/'.$path);
        $finder->shouldReceive('name')->once()->with($name);
        $finder->shouldReceive('getIterator')->once()->andReturnUsing(function () use ($basePath, $path) {
            return array_map(function ($file) {
                $fileinfo = m::mock('SplFileInfo');
                $fileinfo->shouldReceive('getRealPath')->andReturn($file);

                return $fileinfo;
            }, Glob::glob($basePath.'/'.$path.'/'));
        });
        $this->assertNull($command->handle());
    }

    public function testHandleDeleteAndThrowException()
    {
        $command = new Find(
            $finder = m::mock('Symfony\Component\Finder\Finder'),
            $files = m::mock(new Filesystem)
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput);

        $input->shouldReceive('getArgument')->once()->with('path')->andReturn($path = 'foo');
        $input->shouldReceive('getOption')->once()->with('name')->andReturn($name = 'foo');
        $input->shouldReceive('getOption')->once()->with('type')->andReturn(null);
        $input->shouldReceive('getOption')->once()->with('maxdepth')->andReturn(null);
        $input->shouldReceive('getOption')->once()->with('delete')->andReturn($delete = 'true');

        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $basePath = $this->root->url();

        $finder->shouldReceive('in')->once()->with($basePath.'/'.$path);
        $finder->shouldReceive('name')->once()->with($name);
        $finder->shouldReceive('getIterator')->once()->andReturnUsing(function () use ($basePath, $path) {
            return array_map(function ($file) {
                $fileinfo = m::mock('SplFileInfo');
                $fileinfo->shouldReceive('getRealPath')->andReturn($file);

                return $fileinfo;
            }, Glob::glob($basePath.'/'.$path.'/'));
        });
        $files->shouldReceive('isDirectory')->andThrow(new Exception());
        $this->assertNull($command->handle());
    }

    public function testRun()
    {
        $command = new Find(
            $finder = m::mock('Symfony\Component\Finder\Finder'),
            $files = m::mock('Illuminate\Filesystem\Filesystem')
        );

        $command->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application')
        );

        $laravel->shouldReceive('make')->andReturnUsing(function ($className, $parameters) {
            return $parameters['output'];
        });

        $laravel->shouldReceive('call')->once();

        $command->run(new StringInput('./ -name * -type d -maxdepth 0 -delete'), new BufferedOutput);

        $reflectionClass = new \ReflectionClass($command);

        $property = $reflectionClass->getProperty('input');
        $property->setAccessible(true);
        $this->assertSame("'./' -N '*' -T d -M 0 -d true", str_replace('"', "'", (string) $property->getValue($command)));
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
