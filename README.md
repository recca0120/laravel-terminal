# Laravel Web Artisan

[![Latest Stable Version](https://poser.pugx.org/recca0120/terminal/v/stable)](https://packagist.org/packages/recca0120/terminal)
[![Total Downloads](https://poser.pugx.org/recca0120/terminal/downloads)](https://packagist.org/packages/recca0120/terminal)
[![Latest Unstable Version](https://poser.pugx.org/recca0120/terminal/v/unstable)](https://packagist.org/packages/recca0120/terminal)
[![License](https://poser.pugx.org/recca0120/terminal/license)](https://packagist.org/packages/recca0120/terminal)
[![Monthly Downloads](https://poser.pugx.org/recca0120/terminal/d/monthly)](https://packagist.org/packages/recca0120/terminal)
[![Daily Downloads](https://poser.pugx.org/recca0120/terminal/d/daily)](https://packagist.org/packages/recca0120/terminal)

## Installation

Add Presenter to your composer.json file:

```js
"require": {
    "recca0120/terminal": "^2.0.5"
}
```
Now, run a composer update on the command line from the root of your project:

```
composer update
```

### Registering the Package

Include the service provider within `app/config/app.php`. The service povider is needed for the generator artisan command.

```php
'providers' => [
    ...
    Recca0120\Terminal\ServiceProvider::class,
    ...
];
```

publish

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

## Available Commands

*   artisan
*   artisan tinker
*   find
*   mysql

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

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class Inspire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment(PHP_EOL.Inspiring::quote().PHP_EOL);
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
        Commands\Inspire::class,
    ];
}

```
## ScreenShot

### Available Commands
![Available Commands](http://2.bp.blogspot.com/-nk9GXV8qLHo/VokElmq9yJI/AAAAAAAANug/Mdv44NXNEvk/s1600/Image%2B5.png)

### Artisan List
![Artisan List](http://4.bp.blogspot.com/-YGc8NC1oOsc/VokEmZjjuDI/AAAAAAAANuk/P5w1G4nQ8Dw/s1600/Image%2B6.png)

### Migrate
![Migrate](http://4.bp.blogspot.com/-BC5ROg--eMk/VokEm3d30gI/AAAAAAAANus/YbLK9stiefk/s1600/Image%2B7.png)

### Artisan Tinker
![Tinker](http://1.bp.blogspot.com/-7TA7WDb9lGw/VkKl1a-g3iI/AAAAAAAANrs/5LOBp4tBUdk/s1600/Image%2B7.png)

### Find Command
![Find Command](http://2.bp.blogspot.com/-Cq6ZP7Q9aak/VoXQ3zlvxdI/AAAAAAAANtg/XkrAbxvB54c/s1600/Image%2B2.png)

### Find and Delete
![Find and Delete](http://4.bp.blogspot.com/-EH88LYVqH_s/VoXQ39EjRaI/AAAAAAAANtk/kS-RxatY1Kc/s1600/Image%2B4.png)
