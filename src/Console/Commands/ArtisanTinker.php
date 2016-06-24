<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;

class ArtisanTinker extends Command
{
    use Traits\CommandString;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tinker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'artisn tinker';

    /**
     * handle.
     *
     * @return void
     */
    public function handle()
    {
        $command = $this->argument('command');
        $code = trim(trim($command), ';').';';
        $result = null;
        if (strpos($code, 'echo') !== false || strpos($code, 'var_dump') !== false) {
            ob_start();
            eval($code);
            $output = ob_get_clean();
            $this->line(trim($output));
        } else {
            eval('$result = '.$code);
        }

        $this->getOutput()->write('=> ');
        switch (gettype($result)) {
            case 'object';
            case 'array':
                    $this->line(var_export($result, true));
                break;
            case 'string':
                    $this->comment($result);
                break;
            case 'number':
            case 'integer':
            case 'float':
                $this->info($result);
                break;
            default:
                $this->line($result);
                break;
        }
    }
}
