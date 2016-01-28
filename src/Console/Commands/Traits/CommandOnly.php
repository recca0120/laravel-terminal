<?php

namespace Recca0120\Terminal\Console\Commands\Traits;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait CommandOnly
{
    /**
     * get command.
     *
     * @return string
     */
    protected function command()
    {
        return $this->argument('command');
    }

    /**
     * change to array input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return [\Symfony\Component\Console\Input\InputInterface
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $input = new ArrayInput([
            'command' => array_get(app('request')->get('cmd'), 'rest'),
        ]);

        return parent::run($input, $output);
    }
}
