<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use PDO;
use Recca0120\Terminal\Console\Commands\Traits\CommandString;

class Mysql extends Command
{
    use CommandString;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mysql';

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
        $query = $this->argument('command');
        $rows = $connection->select($query);
        $headers = array_keys(array_get($rows, 0, []));
        $this->table($headers, $rows);
    }
}
