<?php

namespace Recca0120\Terminal\Console\Commands;

use org\bovigo\vfs\vfsStream;
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
        $root = vfsStream::setup('tinker');
        $file = vfsStream::newFile('tinker.php')->at($root);

        $command = $this->option('command');
        $code = trim(trim($command), ';').';';
        $result = null;
        if (strpos($code, 'echo') !== false || strpos($code, 'var_dump') !== false) {
            ob_start();
            require $file->withContent('<?php '.$code)->url();
            $this->line(ob_get_clean());
        } else {
            $result = require $file->withContent('<?php return '.$code)->url();
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
                is_numeric($result) === true ?
                    $this->info($result) : $this->line($result);
                break;
        }
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
