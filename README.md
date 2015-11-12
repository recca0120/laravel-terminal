# Laravel Artisan Terminal

![Terminal](http://3.bp.blogspot.com/-XdxaIZCHCxA/VkI0nHtwUNI/AAAAAAAANrY/NEqYZio-cPQ/s1600/Image%2B3.png)

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

open url
http://localhost/path/to/terminal


