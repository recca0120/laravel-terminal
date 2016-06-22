<?php

namespace Recca0120\Terminal\Console\Commands\Traits;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait CommandString
{
    /**
     * change to array input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $command = explode(' ', (string) $input);
        array_shift($command);
        foreach ($command as $key => $value) {
            $command[$key] = preg_replace('/^[\'"]|[\'"]$/', '', $value);
        }
        $input = new ArrayInput([
            'command' => implode(' ', $command),
        ]);

        return parent::run($input, $output);
    }
}
