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
    "recca0120/terminal": "^1.3.3"
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
```bash
$ help
```
![Available Commands](https://cdn.rawgit.com/recca0120/terminal/master/screenshots/available-commands.png)

### Artisan List
```bash
$ artisan
```
![Artisan List](https://cdn.rawgit.com/recca0120/terminal/master/screenshots/artisan-list.png)

### Migrate
```bash
$ artisan migrate --seed
```
![Migrate](https://cdn.rawgit.com/recca0120/terminal/master/screenshots/artisan-migrate.png)

### Artisan Tinker
```bash
$ artisan tinker
```
![Tinker](https://cdn.rawgit.com/recca0120/terminal/master/screenshots/artisan-tinker.png)

### Find Command
```bash
$ find ./ -name * -maxdepth 1
```
![Find Command](https://cdn.rawgit.com/recca0120/terminal/master/screenshots/find-command.png)

### Find and Delete
```bash
$ find ./storage/logs -name * -maxdepth 1 -delete
```
![Find and Delete](https://cdn.rawgit.com/recca0120/terminal/master/screenshots/find-and-delete.png)

### Vi
```bash
$ vi server.php
```
![Vi Command](https://cdn.rawgit.com/recca0120/terminal/master/screenshots/vi-command.png)

![Vi Editor](https://cdn.rawgit.com/recca0120/terminal/master/screenshots/vi-editor.png)

![Vi Save](https://cdn.rawgit.com/recca0120/terminal/master/screenshots/vi-save.png)

### Tail
```bash
$ tail
$ tail --line=1
$ tail server.php
$ tail server.php --line 5
```
![Tail Command](https://cdn.rawgit.com/recca0120/terminal/master/screenshots/tail-command.png)


### Cleanup
```bash
$ cleanup
```
![Cleanup Command](https://cdn.rawgit.com/recca0120/terminal/master/screenshots/cleanup-command.png)
