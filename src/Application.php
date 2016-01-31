<?php

namespace Recca0120\Terminal;

use Illuminate\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    /**
     * only register custom command.
     *
     * @param string $commands
     * @param bool   $customCommand
     *
     * @return $this
     */
    public function resolveCommands($commands, $loadCommand = false)
    {
        if ($loadCommand === false) {
            return $this;
        }

        return parent::resolveCommands($commands);
    }
}
