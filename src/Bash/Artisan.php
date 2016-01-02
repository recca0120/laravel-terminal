<?php

namespace Recca0120\Terminal\Bash;

use Exception;
use Illuminate\Contracts\Console\Kernel as ArtisanContract;
use Illuminate\Http\Request;
use Recca0120\Terminal\ConsoleStyle;
use Symfony\Component\Console\Input\ArrayInput;

class Artisan
{
    protected $artisan;

    protected $request;

    protected $cmd;

    public function __construct(ArtisanContract $artisan, Request $request)
    {
        $this->artisan = $artisan;
        $this->request = $request;
        $this->cmd = $this->request->get('cmd');
    }

    public function execute()
    {
        $args = array_get($this->cmd, 'args');

        return json_encode($this->cmd);
        $result = null;
        $error = null;

        $command = $this->request->get('method', 'list');
        $temp = array_map(function ($item) {
            if (starts_with($item, '--') && strpos($item, '=') === false) {
                $item .= '=default';
            }

            return $item;
        }, $this->request->get('params', []));
        $parameters = [];
        foreach ($temp as $tmp) {
            $explodeTmp = explode('=', $tmp);
            $parameters[array_get($explodeTmp, 0)] = array_get($explodeTmp, 1, '');
        }

        try {
            $parameters['command'] = $command;
            $result = ConsoleStyle::bufferedOutput(function ($bufferedOutput) use ($artisan, $parameters) {
                $exitCode = $artisan->handle(new ArrayInput($parameters), $bufferedOutput);
            });
        } catch (Exception $e) {
            $result = false;
            $error = $e->getMessage();
        }
    }
}
