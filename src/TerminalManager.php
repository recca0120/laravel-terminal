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
        $this->getKernel()->call('--ansi');

        $defaults = [
            'username' => 'LARAVEL',
            'hostname' => php_uname('n'),
            'os' => PHP_OS,
            'helpInfo' => $this->getKernel()->output(),
        ];

        return array_merge($defaults, Arr::except($this->config, [
            'enabled',
            'whitelists',
            'route',
            'commands',
        ]));
    }

    public function getKernel()
    {
        return $this->kernel;
    }
}
