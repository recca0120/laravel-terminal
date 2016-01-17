<?php

namespace Recca0120\Terminal\Console;

use Illuminate\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    /**
     * only register custom command.
     *
     * @param  string $commands
     * @param  bool $customCommand
     * @return $this
     */
    public function resolveCommands($commands, $customCommand = false)
    {
        if ($customCommand === false) {
            return $this;
        }

        return parent::resolveCommands($commands);
    }
}
