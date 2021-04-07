<?php

namespace Recca0120\Terminal;

use Exception;
use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Http\Request;
use Recca0120\Terminal\Contracts\TerminalCommand;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends ConsoleApplication
{
    /**
     * Run an Artisan console command by name.
     *
     * @param string $command
     * @param array $parameters
     * @param OutputInterface $outputBuffer
     * @return int
     * @throws Exception
     */
    public function call($command, array $parameters = [], $outputBuffer = null)
    {
        if ($this->ajax() === true) {
            $this->lastOutput = $outputBuffer ?: new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true, new OutputFormatter(true));
            $this->setCatchExceptions(true);
        } else {
            $this->lastOutput = $outputBuffer ?: new BufferedOutput();
            $this->setCatchExceptions(false);
        }

        $command .= ' '.implode(' ', $parameters);
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
     * @return $this
     */
    public function resolveCommands($commands)
    {
        return parent::resolveCommands(array_filter($commands, static function ($command) {
            return is_subclass_of($command, TerminalCommand::class);
        }));
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
