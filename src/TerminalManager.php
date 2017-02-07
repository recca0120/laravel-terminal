<?php

namespace Recca0120\Terminal;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Foundation\Application as Laravel;

class TerminalManager
{
    protected $kernel;
    protected $config;

    public function __construct(Kernel $kernel, $config = [])
    {
        $this->kernel = $kernel;
        $this->config = $config;
    }

    public function getOptions()
    {
        $this->call('--ansi');

        return Arr::except(array_merge([
            'username' => 'LARAVEL',
            'hostname' => php_uname('n'),
            'os' => PHP_OS,
            'helpInfo' => $this->output(),
        ], $this->config), [
            'enabled',
            'whitelists',
            'route',
            'commands',
        ]);
    }

    public function call($command)
    {
        return $this->kernel->call($command);
    }

    public function output()
    {
        return $this->kernel->output();
    }
}
