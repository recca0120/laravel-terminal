# Laravel Artisan Terminal

## ScreenShot

### Available Commands
![Available Commands](http://3.bp.blogspot.com/-weTE1ATHsCk/VoXQ3w1LBdI/AAAAAAAANto/GICTx7uaUU8/s1600/Image%2B3.png)

### Find Command
![Find Command](http://2.bp.blogspot.com/-Cq6ZP7Q9aak/VoXQ3zlvxdI/AAAAAAAANtg/XkrAbxvB54c/s1600/Image%2B2.png)

### Find and Delete
![Find and Delete](http://4.bp.blogspot.com/-EH88LYVqH_s/VoXQ39EjRaI/AAAAAAAANtk/kS-RxatY1Kc/s1600/Image%2B4.png)

### Migrate
![Migrate](https://scontent.xx.fbcdn.net/hphotos-xaf1/v/t1.0-9/12241564_10153765130179181_8141423366741313826_n.jpg?oh=0fe3e3e13de3c8a1983f1577951fc562&oe=56F1301E)

### Artisan List
![Artisan List](http://3.bp.blogspot.com/-XdxaIZCHCxA/VkI0nHtwUNI/AAAAAAAANrY/NEqYZio-cPQ/s1600/Image%2B3.png)

### Artisan Tinker
![Tinker](http://1.bp.blogspot.com/-7TA7WDb9lGw/VkKl1a-g3iI/AAAAAAAANrs/5LOBp4tBUdk/s1600/Image%2B7.png)

## Installation

```
composer require recca0120/terminal
```

OR

Update composer.json
```
{
    "require": {
        ...
        "recca0120/terminal": "~0.1"
    },
}
```

Require this package with composer:

```
composer update
```
### Laravel 5.1:

Update config/app.php

```php
'providers' => [
    ...
    Recca0120\Terminal\ServiceProvider::class,
];
```

```php
artisan vendor:publish --provider="Recca0120\Terminal\ServiceProvider"
```

### URL

http://localhost/path/to/terminal

### Whitelist
```php
return [
    'whitelists' => ['127.0.0.1', 'your ip'],
];

```

### Find

not full support, but you can delete file use this function (please check file permission)

```bash
find ./vendor -name tests -type d -maxdepth 4 -delete
```

## Add Your Command

### Add Command Class
```php
// src/Console/Commands/Mysql.php

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
```

### Add Command
```php
// src/Console/Kernel.php
namespace Recca0120\Terminal\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Recca0120\Terminal\Console\Application as Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Artisan::class,
        Commands\ArtisanTinker::class,
        Commands\Mysql::class,
        Commands\Find::class,
    ];

    /**
     * Get the Artisan application instance.
     *
     * @return \Illuminate\Console\Application
     */
    protected function getArtisan()
    {
        if (is_null($this->artisan)) {
            return $this->artisan = (new Artisan($this->app, $this->events, $this->app->version()))
                                ->resolveCommands($this->commands, true);
        }

        return $this->artisan;
    }
}

```
