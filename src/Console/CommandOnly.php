<?php

namespace Recca0120\Terminal\Console;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait CommandOnly
{
    protected function command()
    {
        return with(new ArgvInput($this->argv()))->getFirstArgument();
    }

    protected function argv()
    {
        return preg_split('/\s+/', array_get($this->argument('command'), 'command'));
    }

    protected function rest()
    {
        return array_get($this->argument('command'), 'rest');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $input = new ArrayInput([
            'command' => app('request')->get('cmd'),
        ]);

        return parent::run($input, $output);
    }
}
