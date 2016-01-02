<?php

namespace Recca0120\Terminal\Http\Controllers;

use Artisan;
use DB;
use Exception;
use File;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use PDO;
use Recca0120\Terminal\ConsoleStyle;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Finder\Finder;

class TerminalController extends Controller
{
    private $request;

    public function index(Application $app)
    {
        $environment = $app->environment();

        return view('terminal::index2', compact('environment'));
    }

    public function rpcResponse(Request $request)
    {
        $error = false;

        return response()->json([
            'jsonrpc' => $request->get('jsonrpc'),
            'id' => $request->get('id'),
            'result' => json_encode($request->all()),
            'error' => $error,
        ]);
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

    public function postArtisan()
    {
        // set_time_limit(30);
        $result = null;
        $error = null;

        $command = $this->request->get('method', 'list');
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
            $result = ConsoleStyle::bufferedOutput(function ($bufferedOutput) use ($parameters) {
                $exitCode = Artisan::handle(new ArrayInput($parameters), $bufferedOutput);
            });
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

        try {
            if (starts_with($command, '-') === true) {
                $parameters = array_merge([$command], $parameters);
                $finder->in($basePath);
            } else {
                $finder->in($basePath.'/'.$command);
            }

            $name = $this->parseFinderArgument($parameters, '-name');
            $type = $this->parseFinderArgument($parameters, '-type');
            $maxDepth = $this->parseFinderArgument($parameters, '-maxdepth');
            $delete = $this->parseFinderArgument($parameters, '-delete', true);
            $exec = $this->parseFinderArgument($parameters, '-exec');

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

            if ($maxDepth !== false) {
                if ($maxDepth == '0') {
                    return $this->rpcResponse('./', $error);
                }
                $finder->depth('<'.$maxDepth);
            }

            $result = [];
            foreach ($finder as $file) {
                $realPath = $file->getRealpath();
                $result[] = $realPath;
                // $relativePathname = str_replace($basePath, '', $file->getRealpath());
                // $result[] = '.'.str_replace('\\', '/', $relativePathname);
                // $result[] = $file->getRealpath();
                // $result[] = './'.str_replace('\\', '/', $file->getRelativePathname());
            }

            if ($delete === true) {
                foreach ($result as $key => $realPath) {
                    $deleted = false;
                    if (File::exists($realPath) === true) {
                        if (File::isDirectory($realPath) === true) {
                            $deleted = File::deleteDirectory($realPath);
                        } else {
                            $deleted = File::delete($realPath);
                        }
                    }
                    if ($deleted === true) {
                        $result[$key] = ConsoleStyle::info($realPath.' deleted');
                    } else {
                        $result[$key] = ConsoleStyle::error($realPath.' isnt deleted');
                    }
                }
            }

            $result = implode("\n", $result);
        } catch (InvalidArgumentException $e) {
            $error = true;
            $result = $e->getMessage();
            // $result = str_replace('/./', './', str_replace($basePath, '', $e->getMessage()));
        }

        return $this->rpcResponse($result, $error);
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
            $result = ConsoleStyle::table($rows, $headers);
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
                    echo ConsoleStyle::comment($returnValue);
                    break;
                default:
                    echo ConsoleStyle::info($returnValue);
                    break;
            }
            $result = ob_get_clean();
            $result = '==> '.$result;
        } catch (Exception $e) {
            $result = false;
            $error = $e->getMessage();
        }

        return $this->rpcResponse($result, $error);
    }

    protected function parseFinderArgument(&$parameters, $argumentName, $onlyArgumentName = false)
    {
        $length = count($parameters) - 1;
        foreach ($parameters as $key => $value) {
            if ($value === $argumentName) {
                if ($onlyArgumentName === true) {
                    unset($parameters[$key]);

                    return true;
                }

                if ($key === $length) {
                    return false;
                }

                $next = $parameters[$key + 1];
                unset($parameters[$key]);
                unset($parameters[$key + 1]);

                return $next;
            }
        }

        return false;
    }

    // protected function rpcResponse($result, $error)
    // {
    //     if ($error === true) {
    //         $result = ConsoleStyle::error($result);
    //     }

    //     return response()->json([
    //         'jsonrpc' => $this->request->get('jsonrpc'),
    //         'result' => $result,
    //         'id' => $this->request->get('id'),
    //         'error' => $error,
    //     ]);
    // }
}
