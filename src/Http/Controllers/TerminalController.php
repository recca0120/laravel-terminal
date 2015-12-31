<?php

namespace Recca0120\Terminal\Http\Controllers;

use Artisan;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use PDO;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Finder\Finder;

class TerminalController extends Controller
{
    private $outputFormatter;

    private $request;

    public function __construct(Request $request)
    {
        $this->outputFormatter = new OutputFormatter(true);

        $this->request = $request;
    }

    public function getIndex()
    {
        $environment = app()->environment();
        $reflectionClass = new ReflectionClass($this);
        $endPoints = collect($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC))
        ->filter(function ($method) {
            return starts_with($method->name, 'post');
        })->map(function ($method) {
            return [
                'key' => substr(strtolower($method->name), 4),
                'url' => action('\\'.$method->class.'@'.$method->name),
            ];
        })->pluck('url', 'key')->toJson();

        return view('terminal::index', compact('environment', 'endPoints'));
    }

    public function postMysql()
    {
        $result = null;
        $error = null;
        $command = $this->request->get('method');

        try {
            DB::setFetchMode(PDO::FETCH_ASSOC);
            $rows = DB::select($command);
            $headers = array_keys(array_get($rows, 0, []));

            $lastOutput = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true, $this->outputFormatter);
            $table = new Table($lastOutput);

            $table
                ->setHeaders($headers)
                ->setRows($rows)
                ->setStyle('default')
                ->render();

            $result = $lastOutput->fetch();
        } catch (Exception $e) {
            $result = false;
            $error = $e->getMessage();
        }

        return $this->rpcResponse($result, $error);
    }

    public function postTinker()
    {
        $result = null;
        $error = null;
        $command = $this->request->get('method');
        $command = trim($command, ';').';';
        try {
            ob_start();
            $returnValue = eval('return '.$command);
            switch (gettype($returnValue)) {
                case 'object':
                case 'array':
                    var_dump($returnValue);
                    break;
                case 'string':
                    echo '<comment>"'.$returnValue.'"</comment>';
                    break;
                default:
                    echo '<info>'.$returnValue.'</info>';
                    break;
            }
            $result = ob_get_clean();
            $result = '==> '.$this->outputFormatter->format($result);
        } catch (Exception $e) {
            $result = false;
            $error = $e->getMessage();
        }

        return $this->rpcResponse($result, $error);
    }

    public function postArtisan()
    {
        // set_time_limit(30);
        $result = null;
        $error = null;

        $command = $this->request->get('method');
        $temp = array_map(function ($item) {
            if (starts_with($item, '--') && strpos($item, '=') === false) {
                $item .= '=default';
            }

            return $item;
        }, $this->request->get('params', []));
        $parameters = [];
        foreach ($temp as $tmp) {
            $explodeTmp = explode('=', $tmp);
            $parameters[array_get($explodeTmp, 0)] = array_get($explodeTmp, 1, '');
        }

        try {
            $parameters['command'] = $command;
            $lastOutput = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true, $this->outputFormatter);
            $exitCode = Artisan::handle(new ArrayInput($parameters), $lastOutput);
            $result = $lastOutput->fetch();
            // exit;
            // $exitCode = Artisan::call($command, $parameters);
            // $result = Artisan::output();
        } catch (Exception $e) {
            $result = false;
            $error = $e->getMessage();
        }

        return $this->rpcResponse($result, $error);
    }

    public function postFind()
    {
        $error = false;
        $command = $this->request->get('method');
        $parameters = $this->request->get('params', []);
        $basePath = base_path();
        $finder = new Finder();
        $finder
            ->ignoreVCS(false)
            ->ignoreDotFiles(false);

        $name = $this->parseFinderArgument($parameters, '-name');
        $type = $this->parseFinderArgument($parameters, '-type');
        $maxdepth = $this->parseFinderArgument($parameters, '-maxdepth');

        if ($name !== false) {
            $finder->name($name);
        }

        switch ($type) {
            case 'd':
                $finder->directories();
                break;
            case 'f':
                $finder->files();
                break;
        }

        if ($maxdepth !== false) {
            if ($maxdepth == '0') {
                return $this->rpcResponse('./', $error);
            }
            $finder->depth('<'.$maxdepth);
        }

        try {
            if (starts_with($command, '-') === true) {
                $parameters = array_merge([$command], $parameters);
                $finder->in($basePath);
            } else {
                $finder->in($basePath.'/'.$command);
            }

            $result = [];
            foreach ($finder as $file) {
                // $relativePathname = str_replace($basePath, '', $file->getRealpath());
                // $result[] = '.'.str_replace('\\', '/', $relativePathname);
                // $result[] = $file->getRealpath();
                // $result[] = './'.str_replace('\\', '/', $file->getRelativePathname());
                $result[] = $file->getRealpath();
            }
            $result = implode("\n", $result);
        } catch (InvalidArgumentException $e) {
            $error = true;
            $result = $e->getMessage();
            // $result = str_replace('/./', './', str_replace($basePath, '', $e->getMessage()));
        }

        return $this->rpcResponse($result, $error);
    }

    protected function parseFinderArgument(&$parameters, $argumentName)
    {
        $length = count($parameters) - 1;
        foreach ($parameters as $key => $value) {
            if ($key === $length) {
                return false;
            }
            if ($value === $argumentName) {
                $next = $parameters[$key + 1];
                unset($parameters[$key]);
                unset($parameters[$key + 1]);

                return $next;
            }
            // if (in_array($argumentName, $argumentList) === true) {
            //     if ($key + 1 > $length) {
            //         return false;
            //     }
            //     $next = $parameters[$value + 1];

            //     return $next;
            // }
        }

        return false;
    }

    protected function rpcResponse($result, $error)
    {
        // $command = $this->request->get('command');
        return response()->json([
            'jsonrpc' => '2.0',
            'result' => $result,
            'id' => $this->request->get('id'),
            'error' => $error,
        ]);
    }
}
