<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Recca0120\Terminal\Contracts\WebCommand;
use Symfony\Component\Console\Input\InputOption;

class Mysql extends Command implements WebCommand
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
     * Handle the command.
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        $query = $this->option('command');
        if(isset(config('terminal.dbconnection')) {
            $connection = $this->databaseManager->connection(config('terminal.dbconnection'));
        } else {
            $connection = $this->databaseManager->connection();
        }
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
