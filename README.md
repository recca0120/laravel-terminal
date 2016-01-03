# Laravel Terminal (Web Console)

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
        "recca0120/terminal": "~2.0"
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

## ScreenShot

### Available Commands
![Available Commands](http://3.bp.blogspot.com/-QRSwzua3ssw/Voj6R_R9YsI/AAAAAAAANuA/14DvmOeCqyg/s1600/Image%2B1.png)

### Artisan List
![Artisan List](http://4.bp.blogspot.com/-c_LbDrG1tRE/Voj6SgkOhsI/AAAAAAAANuQ/XItrph1cVXM/s1600/Image%2B3.png)

### Migrate
![Migrate](http://2.bp.blogspot.com/-Rdjxurg2VTQ/Voj6SKydw7I/AAAAAAAANuE/kcb--ehZ9ps/s1600/Image%2B2.png)

### Artisan Tinker
![Tinker](http://1.bp.blogspot.com/-7TA7WDb9lGw/VkKl1a-g3iI/AAAAAAAANrs/5LOBp4tBUdk/s1600/Image%2B7.png)

### Find Command
![Find Command](http://2.bp.blogspot.com/-Cq6ZP7Q9aak/VoXQ3zlvxdI/AAAAAAAANtg/XkrAbxvB54c/s1600/Image%2B2.png)

### Find and Delete
![Find and Delete](http://4.bp.blogspot.com/-EH88LYVqH_s/VoXQ39EjRaI/AAAAAAAANtk/kS-RxatY1Kc/s1600/Image%2B4.png)

<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- github -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-4164400566432410"
     data-ad-slot="9584820886"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
