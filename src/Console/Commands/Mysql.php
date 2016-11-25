<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use PDO;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Arr;

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
     *
     * @method fire
     */
    public function fire()
    {
        $connection = $this->databaseManager->connection();
        $connection->setFetchMode(PDO::FETCH_ASSOC);
        $query = $this->option('command');
        $rows = $connection->select($query);
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
