<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use PDO;
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
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * __construct.
     *
     * @param \Illuminate\Database\ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    /**
     * fire.
     *
     * @method fire
     *
     * @return void
     */
    public function fire()
    {
        $this->connection->setFetchMode(PDO::FETCH_ASSOC);
        $query = $this->option('command');
        $rows = $this->connection->select($query);
        $headers = array_keys(array_get($rows, 0, []));
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
