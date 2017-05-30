<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ArtisanTinker extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tinker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'artisn tinker';

    /**
     * fire.
     */
    public function fire()
    {
        $command = $this->option('command');

        ob_start();
        $result = $this->executeCode(
            trim(trim($command), ';').';'
        );
        $output = ob_get_clean();

        if (empty($output) === false) {
            $this->line($output);
        }

        $this->getOutput()->write('=> ');
        switch (gettype($result)) {
            case 'object':
            case 'array':
                $this->line(var_export($result, true));
                break;
            case 'string':
                $this->comment($result);
                break;
            default:
                is_numeric($result) === true ? $this->info($result) : $this->line($result);
                break;
        }
    }

    protected function executeCode($code)
    {
        $result = null;
        if (strpos($code, 'echo') !== false || strpos($code, 'var_dump') !== false) {
            eval($code);
        } else {
            eval('$result = '.$code);
        }

        return $result;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['command', null, InputOption::VALUE_OPTIONAL],
        ];
    }
}
