<?php
/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

use Carbon\Carbon;
use Illuminate\Container\Container;

if (class_exists('PHPUnit\Framework\TestCase') === false) {
    class_alias('PHPUnit_Framework_TestCase', 'PHPUnit\Framework\TestCase');
}

/*
|--------------------------------------------------------------------------
| Set The Default Timezone
|--------------------------------------------------------------------------
|
| Here we will set the default timezone for PHP. PHP is notoriously mean
| if the timezone is not explicitly set. This will be used by each of
| the PHP date and date-time functions throughout the application.
|
*/

date_default_timezone_set('UTC');

Carbon::setTestNow(Carbon::now());

if (function_exists('env') === false) {
    function env($env)
    {
        switch ($env) {
            case 'APP_ENV':
                return 'local';
                break;

            case 'APP_DEBUG':
                return true;
                break;
        }
    }
}

if (function_exists('public_path') === false) {
    function public_path($path = '')
    {
        return ''.$path;
    }
}

if (function_exists('config_path') === false) {
    function config_path($path = '')
    {
        return __DIR__.'/../config/'.$path;
    }
}

if (function_exists('base_path') === false) {
    function base_path($path = '')
    {
        return Container::getInstance()->basePath($path);
    }
}

if (function_exists('storage_path') === false) {
    function storage_path()
    {
        return Container::getInstance()->make('path.storage');
    }
}

if (class_exists('Route') === false) {
    class bootstrap
    {
        public static function __callStatic($method, $arguments)
        {
            return new static;
        }

        public function __call($method, $arguments)
        {
        }
    }
}
