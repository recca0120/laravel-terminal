<?php

namespace Recca0120\Terminal\Console;

use Illuminate\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    public function resolveCommands($commands)
    {
        foreach ($commands as $key => $command) {
            if (strpos($command, __NAMESPACE__) !== 0) {
                unset($commands[$key]);
            }
        }

        if (count($commands) > 0) {
            parent::resolveCommands($commands);
        }

        return $this;
    }
}
