<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use PDO;

class Mysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mysql {--command=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'mysql';

    /**
     * handle.
     *
     * @param \Illuminate\Database\ConnectionInterface $connection
     *
     * @return void
     */
    public function handle(ConnectionInterface $connection)
    {
        $connection->setFetchMode(PDO::FETCH_ASSOC);
        $query = $this->option('command');
        $rows = $connection->select($query);
        $headers = array_keys(array_get($rows, 0, []));
        $this->table($headers, $rows);
    }
}
