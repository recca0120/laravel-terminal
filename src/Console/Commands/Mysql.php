<?php

namespace Recca0120\Terminal\Console\Commands;

use DB;
use Illuminate\Console\Command;
use PDO;

class Mysql extends Command
{
    use Traits\CommandOnly;
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

    public function handle()
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $query = $this->argument('command');
        $rows = DB::select($query);
        $headers = array_keys(array_get($rows, 0, []));
        $this->table($headers, $rows);
    }
}
