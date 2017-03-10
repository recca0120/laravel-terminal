 [![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YNNLC9V28YDPN)

# Laravel Web Artisan

[![StyleCI](https://styleci.io/repos/45892521/shield?style=flat)](https://styleci.io/repos/45892521)
[![Build Status](https://travis-ci.org/recca0120/laravel-terminal.svg)](https://travis-ci.org/recca0120/laravel-terminal)
[![Total Downloads](https://poser.pugx.org/recca0120/terminal/d/total.svg)](https://packagist.org/packages/recca0120/terminal)
[![Latest Stable Version](https://poser.pugx.org/recca0120/terminal/v/stable.svg)](https://packagist.org/packages/recca0120/terminal)
[![Latest Unstable Version](https://poser.pugx.org/recca0120/terminal/v/unstable.svg)](https://packagist.org/packages/recca0120/terminal)
[![License](https://poser.pugx.org/recca0120/terminal/license.svg)](https://packagist.org/packages/recca0120/terminal)
[![Monthly Downloads](https://poser.pugx.org/recca0120/terminal/d/monthly)](https://packagist.org/packages/recca0120/terminal)
[![Daily Downloads](https://poser.pugx.org/recca0120/terminal/d/daily)](https://packagist.org/packages/recca0120/terminal)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/recca0120/laravel-terminal/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/recca0120/laravel-terminal/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/recca0120/laravel-terminal/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/recca0120/laravel-terminal/?branch=master)

## Installation

Add Presenter to your composer.json file:

```js
"require": {
    "recca0120/terminal": "^1.6.8"
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
    Recca0120\Terminal\TerminalServiceProvider::class,
    ...
];
```

publish

```php
artisan vendor:publish --provider="Recca0120\Terminal\TerminalServiceProvider"
```


### URL

http://localhost/path/to/terminal

### config

```php
return [
    'enabled'    => env('APP_DEBUG'),
    'whitelists' => ['127.0.0.1', 'your ip'],
    'route'     => [
        'prefix'     => 'terminal',
        'as'         => 'terminal.',
        'middleware' => ['web'], // if you use laravel 5.1 remove web
    ],
    'commands' => [
        \Recca0120\Terminal\Console\Commands\Artisan::class,
        \Recca0120\Terminal\Console\Commands\ArtisanTinker::class,
        \Recca0120\Terminal\Console\Commands\Cleanup::class,
        \Recca0120\Terminal\Console\Commands\Find::class,
        \Recca0120\Terminal\Console\Commands\Mysql::class,
        \Recca0120\Terminal\Console\Commands\Tail::class,
        \Recca0120\Terminal\Console\Commands\Vi::class,
        // add your command
    ],
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

## ScreenShot

### Available Commands
```bash
$ help
```
![Available Commands](https://cdn.rawgit.com/recca0120/terminal/master/docs/screenshots/available-commands.png)

### Artisan List
```bash
$ artisan
```
![Artisan List](https://cdn.rawgit.com/recca0120/terminal/master/docs/screenshots/artisan-list.png)

### Migrate
```bash
$ artisan migrate --seed
```
![Migrate](https://cdn.rawgit.com/recca0120/terminal/master/docs/screenshots/artisan-migrate.png)

### Artisan Tinker
```bash
$ artisan tinker
```
![Tinker](https://cdn.rawgit.com/recca0120/terminal/master/docs/screenshots/artisan-tinker.png)

### MySQL
```bash
$ mysql
mysql> select * from users;
```
![MySQL Command](https://cdn.rawgit.com/recca0120/terminal/master/docs/screenshots/mysql-command.png)

### Find Command
```bash
$ find ./ -name * -maxdepth 1
```
![Find Command](https://cdn.rawgit.com/recca0120/terminal/master/docs/screenshots/find-command.png)

### Find and Delete
```bash
$ find ./storage/logs -name * -maxdepth 1 -delete
```
![Find and Delete](https://cdn.rawgit.com/recca0120/terminal/master/docs/screenshots/find-and-delete.png)

### Vi
```bash
$ vi server.php
```
![Vi Command](https://cdn.rawgit.com/recca0120/terminal/master/docs/screenshots/vi-command.png)

![Vi Editor](https://cdn.rawgit.com/recca0120/terminal/master/docs/screenshots/vi-editor.png)

![Vi Save](https://cdn.rawgit.com/recca0120/terminal/master/docs/screenshots/vi-save.png)

### Tail
```bash
$ tail
$ tail --line=1
$ tail server.php
$ tail server.php --line 5
```
![Tail Command](https://cdn.rawgit.com/recca0120/terminal/master/docs/screenshots/tail-command.png)


### Cleanup
```bash
$ cleanup
```
![Cleanup Command](https://cdn.rawgit.com/recca0120/terminal/master/docs/screenshots/cleanup-command.png)
