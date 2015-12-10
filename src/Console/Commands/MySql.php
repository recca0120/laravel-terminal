<?php

namespace Recca0120\Terminal\Console\Commands;

use DB;
use Illuminate\Console\Command;
use PDO;
use Recca0120\Terminal\Console\CommandOnly;

class MySql extends Command
{
    use CommandOnly;
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
        $query = $this->rest();
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $rows = DB::select($query);
        $headers = array_keys(array_get($rows, 0, []));
        $this->table($headers, $rows);
    }
}
