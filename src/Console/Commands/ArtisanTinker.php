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
        $this->getOutput()->write('=> ');
        ob_start();
        if (starts_with($code, 'echo') === false) {
            $code = 'return '.$code;
        }
        $returnValue = eval($code);
        switch (gettype($returnValue)) {
            case 'object':
            case 'array':
                $this->line(var_export($returnValue, true));
                break;
            case 'string':
                $this->info($returnValue);
                break;
            case 'number':
            case 'integer':
            case 'float':
                $this->comment($returnValue);
                break;
            default:
                $this->line($returnValue);
                break;
        }
        $result = ob_get_clean();
        if (empty($result) === false) {
            $this->line(preg_replace("/(\n|\n\r)+/", '', $result));
        }
    }
}
