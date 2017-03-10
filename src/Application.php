<?php

namespace Recca0120\Terminal;

use Illuminate\Http\Request;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Illuminate\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    /**
     * Run an Artisan console command by name.
     *
     * @param string $command
     * @param array $parameters
     * @return int
     */
    public function call($command, array $parameters = [])
    {
        if ($this->ajax() === true) {
            $this->lastOutput = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true, new OutputFormatter(true));
            $this->setCatchExceptions(true);
        } else {
            $this->lastOutput = new BufferedOutput();
            $this->setCatchExceptions(false);
        }

        $command = $command.' '.implode(' ', $parameters);
        $input = new StringInput($command);
        $input->setInteractive(false);

        $result = $this->run($input, $this->lastOutput);

        $this->setCatchExceptions(true);

        return $result;
    }

    /**
     * Resolve an array of commands through the application.
     *
     * @param array|mixed $commands
     * @param bool $web
     * @return $this
     */
    public function resolveCommands($commands, $web = false)
    {
        return $web === true ? parent::resolveCommands($commands) : $this;
    }

    /**
     * ajax.
     *
     * @return bool
     */
    private function ajax()
    {
        $request = $this->laravel['request'] ?: Request::capture();

        return $request->ajax();
    }
}
