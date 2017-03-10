<?php

namespace Recca0120\Terminal;

use Illuminate\Support\Arr;

class TerminalManager
{
    /**
     * $kernel.
     *
     * @var \Recca0120\Terminal\Kernel
     */
    protected $kernel;

    /**
     * $config.
     *
     * @var array
     */
    protected $config;

    /**
     * __construct.
     *
     * @param \Recca0120\Terminal\Kernel $kernel
     * @param array $config
     */
    public function __construct(Kernel $kernel, $config = [])
    {
        $this->kernel = $kernel;
        $this->config = Arr::except(array_merge([
            'username' => 'LARAVEL',
            'hostname' => php_uname('n'),
            'os' => PHP_OS,
        ], $config), [
            'enabled',
            'whitelists',
            'route',
            'commands',
        ]);
    }

    /**
     * getConfig.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * call.
     *
     * @param string $command
     * @return int
     */
    public function call($command)
    {
        return $this->kernel->call($command);
    }

    /**
     * output.
     *
     * @return string
     */
    public function output()
    {
        return $this->kernel->output();
    }
}
