<?php

namespace Recca0120\Terminal;

use Exception;
use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends ConsoleApplication
{
    /**
     * Run an Artisan console command by name.
     *
     * @param string $command
     * @param array  $parameters
     *
     * @return int
     */
    public function call($command, array $parameters = [])
    {
        $this->lastOutput = $this->getBufferedOutput();

        $this->setCatchExceptions(false);

        $command = $command.''.implode(' ', $parameters);
        $result = $this->run(new StringInput($command), $this->lastOutput);

        $this->setCatchExceptions(true);

        return $result;
    }

    /**
     * Resolve an array of commands through the application.
     *
     * @param array|mixed $commands
     *
     * @return $this
     */
    public function resolveCommands($commands)
    {
        if ($this->isFromArtisanStartingEvent() === true) {
            return $this;
        }

        return parent::resolveCommands($commands);
    }

    /**
     * Add a command to the console.
     *
     * @param \Symfony\Component\Console\Command\Command $command
     *
     * @return \Symfony\Component\Console\Command\Command
     */
    public function add(SymfonyCommand $command)
    {
        if ($command instanceof Command) {
            $command->setLaravel($this->laravel);
        }

        return parent::addToParent($command);
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        try {
            return parent::run($input, $output);
        } catch (Exception $e) {
            if ($this->isAjax() === false) {
                throw $e;
            }
            $this->renderException($e, $output);

            return 1;
        } catch (Throwable $e) {
            if ($this->isAjax() === false) {
                throw $e;
            }
            $e = new FatalThrowableError($e);
            $this->renderException($e, $output);

            return 1;
        }
    }

    private function getBufferedOutput()
    {
        if ($this->isAjax() === true) {
            return new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true, new OutputFormatter(true));
        }

        return new BufferedOutput();
    }

    private function isAjax()
    {
        if (is_null($this->laravel['request']) === false) {
            return Request::capture()->ajax();
        }

        return $this->laravel['request']->ajax();
    }

    private function isFromArtisanStartingEvent()
    {
        // Illuminate\Console\Events\ArtisanStarting
        if (is_null($this->laravel['events']) === false) {
            return $this->laravel['events']->firing() !== false;
        }

        return false;
    }
}
