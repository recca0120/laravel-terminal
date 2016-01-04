<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Recca0120\Terminal\Console\CommandOnly;

class ArtisanTinker extends Command
{
    use CommandOnly;
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

    public function handle()
    {
        $code = trim($this->rest(), ';').';';
        $this->output->write('=> ');
        ob_start();
        $returnValue = eval('return '.$code);
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
