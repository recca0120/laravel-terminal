<?php

namespace Recca0120\Terminal\Console\Commands;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use stdClass;
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
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * __construct.
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
        $sql = $this->option('command');
        $connection = $this->databaseManager->connection($this->option('connection'));
        $rows = $this->castArray($connection->select($sql, [], true));
        $headers = array_keys(Arr::get($rows, 0, []));
        $this->table($headers, $rows);
    }

    /**
     * castArray.
     *
     * @param  stdClass[]  $rows
     * @return array[]
     */
    protected function castArray($rows)
    {
        return array_map(static function ($row) {
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
