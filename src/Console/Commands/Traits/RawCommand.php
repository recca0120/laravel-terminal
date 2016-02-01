<?php

namespace Recca0120\Terminal\Console\Commands\Traits;

use Illuminate\Http\Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait RawCommand
{
    /**
     * construct.
     *
     * @param \Illuminate\Contracts\Console\Kernel $artisan
     */
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
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
            'command' => array_get($this->request->get('cmd'), 'rest'),
        ]);

        return parent::run($input, $output);
    }
}
