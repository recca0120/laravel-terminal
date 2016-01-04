<?php

namespace Recca0120\Terminal\Console\Commands\Traits;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait CommandOnly
{
    protected function command()
    {
        return $this->argument('command');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $input = new ArrayInput([
            'command' => array_get(app('request')->get('cmd'), 'rest'),
        ]);

        return parent::run($input, $output);
    }
}
