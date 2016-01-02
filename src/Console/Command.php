<?php

namespace Recca0120\Terminal\Console;

use Illuminate\Console\Command as ConsoleCommand;

class Command extends ConsoleCommand{}

// use Closure;
// use Illuminate\Foundation\Application;
// use Illuminate\Http\Request;
// use Symfony\Component\Console\Formatter\OutputFormatter;
// use Symfony\Component\Console\Helper\Table;
// use Symfony\Component\Console\Input\ArgvInput;
// use Symfony\Component\Console\Output\BufferedOutput;

// abstract class Command
// {
//     protected $request = null;

//     protected $arguments = [];

//     protected $options = [];

//     protected $command;

//     protected $cmd;

//     protected $rest;

//     protected $argv;

//     protected static $outputFormat = null;

//     /**
//      * The name and signature of the console command.
//      *
//      * @var string
//      */
//     protected $signature = '';

//     /**
//      * The console command description.
//      *
//      * @var string
//      */
//     protected $description = 'artisan';

//     /**
//      * Execute the console command.
//      *
//      * @return mixed
//      */
//     abstract public function handle();

//     public function fire()
//     {
//         return $this->handle();
//     }

//     public function setApplication(Application $app)
//     {
//         $this->app = $app;

//         return $this;
//     }

//     public function setRequest(Request $request)
//     {
//         $cmd = $request->get('cmd');
//         $command = trim(array_get($cmd, 'command', ''));
//         $argv = preg_split('/\s+/', $command);
//         $this->argv = new ArgvInput($argv);

//         return $this;
//         // new ArgvInput(array_get());
//         // $this->request = $request;
//         // $cmd = $this->request->get('cmd');
//         // $this->argv = array_merge([array_get($cmd, 'name')], array_get($cmd, 'args', []));
//         // $command = trim(substr(array_get($cmd, 'command'), strlen($this->signature)));
//         // $args = preg_split('/\s+/', $command);
//         // $name = array_shift($args);
//         // $this->cmd = [
//         //     'command' => $command,
//         //     'name' => $name,
//         //     'args' => $args,
//         //     'rest' => trim(substr($command, strlen($name))),
//         // ];

//         // $this->command = array_get($this->cmd, 'command');
//         // $this->rest = array_get($this->cmd, 'rest');
//         // list($arguments, $options) = $this->parseArguments(array_get($this->cmd, 'args', []));
//         // $this->arguments = $arguments;
//         // $this->options = $options;

//         // return $this;
//     }

//     protected function parseArguments($tokens)
//     {
//         $parseOptions = true;
//         $arguments = $tokens;
//         $options = [];
//         foreach ($tokens as $index => $token) {
//             if (starts_with($token, '--') === true) {
//                 $temp = explode('=', substr($token, 2, strlen($token)));
//                 $key = $temp[0];
//                 $value = array_get($temp, 1);
//                 if (isset($options[$key]) === false) {
//                     $options[$key] = [];
//                 }
//                 array_push($options[$key], $value);
//                 unset($arguments[$index]);
//             } elseif (starts_with($token, '-') === true) {
//                 $key = substr($token, 1, strlen($token));
//                 $value = array_get($tokens, $index + 1);
//                 if (starts_with($value, '-') === true or $value === null) {
//                     $value = 'default';
//                 } else {
//                     unset($arguments[$index + 1]);
//                 }
//                 if (isset($options[$key]) === false) {
//                     $options[$key] = [];
//                 }
//                 array_push($options[$key], $value);
//                 unset($arguments[$index]);
//             }
//         }

//         foreach ($options as $key => $value) {
//             if (count($value) == 1) {
//                 $options[$key] = array_get($value, 0);
//             }
//         }

//         return [$arguments, $options];
//     }

//     protected function argument($key = null, $default = null)
//     {
//         if ($key === null) {
//             return $this->arguments;
//         }

//         return array_get($this->arguments, $key, $default);
//     }

//     protected function option($key = null, $default = null)
//     {
//         if ($key === null) {
//             return $this->options;
//         }

//         return array_get($this->options, $key, $default);
//     }

//     protected function getFirstArgument()
//     {
//         return array_get($this->arguments, 0);
//     }

//     protected function rest()
//     {
//         return $this->rest;
//     }

//     public function error($text)
//     {
//         return static::applyFormat($text, __FUNCTION__);
//     }

//     public function info($text)
//     {
//         return static::applyFormat($text, __FUNCTION__);
//     }

//     public function comment($text)
//     {
//         return static::applyFormat($text, __FUNCTION__);
//     }

//     public function question($text)
//     {
//         return static::applyFormat($text, __FUNCTION__);
//     }

//     public function bufferedOutput(Closure $handle)
//     {
//         $bufferedOutput = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true, static::getOutputFormat());
//         $handle($bufferedOutput);

//         return $bufferedOutput->fetch();
//     }

//     public static function getOutputFormat()
//     {
//         if (static::$outputFormat === null) {
//             static::$outputFormat = new OutputFormatter(true);
//         }

//         return static::$outputFormat;
//     }

//     public static function applyFormat($text, $tagName)
//     {
//         return static::getOutputFormat()->format('<'.$tagName.'>'.$text.'</'.$tagName.'>');
//     }

//     public static function table($rows, $headers = [])
//     {
//         return static::bufferedOutput(function ($bufferedOutput) use ($rows, $headers) {
//             $table = new Table($bufferedOutput);
//             $table
//                 ->setHeaders($headers)
//                 ->setRows($rows)
//                 ->setStyle('default')
//                 ->render();
//         });
//     }

//     public function getSignature()
//     {
//         $signature = $this->signature;
//         if (empty($signature) === true) {
//             $signature = base_classname(static::class);
//         }

//         return $signature;
//     }
// }
