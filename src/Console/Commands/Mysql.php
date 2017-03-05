<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Symfony\Component\Console\Input\InputOption;

class Mysql extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'mysql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'mysql console';

    /**
     * $connection.
     *
     * @var \Illuminate\Database\DatabaseManager
     */
    protected $databaseManager;

    /**
     * __construct.
     *
     * @param \Illuminate\Database\DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        parent::__construct();
        $this->databaseManager = $databaseManager;
    }

    /**
     * fire.
     */
    public function fire()
    {
        $query = $this->option('command');
        $connection = $this->databaseManager->connection();
        $rows = json_decode(json_encode($connection->select($query)), true);
        $headers = array_keys(Arr::get($rows, 0, []));
        $this->table($headers, $rows);
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
