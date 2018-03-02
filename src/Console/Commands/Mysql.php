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
        $connection = $this->databaseManager->connection($this->option('connection'));
        $rows = $this->castArray($connection->select($query));
        $headers = array_keys(Arr::get($rows, 0, []));
        $this->table($headers, $rows);
    }

    /**
     * castArray.
     *
     * @param stdClass[] $rows
     * @return void
     */
    protected function castArray($rows)
    {
        return array_map(function ($row) {
            return (array) $row;
        }, $rows);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['command', null, InputOption::VALUE_REQUIRED],
            ['connection', null, InputOption::VALUE_OPTIONAL],
        ];
    }
}
