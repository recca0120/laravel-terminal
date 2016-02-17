<?php

namespace Recca0120\Terminal\Http\Controllers;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Recca0120\Terminal\Console\Kernel as ConsoleKernel;

class TerminalController extends Controller
{
    protected $consoleKernel;

    public function __construct(ConsoleKernel $consoleKernel)
    {
        $this->consoleKernel = $consoleKernel;
    }

    /**
     * index.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Filesystem\Filesystem            $filesystem
     *
     * @return mixed
     */
    public function index(ApplicationContract $app, Filesystem $filesystem, $panel = false)
    {
        $this->consoleKernel->call('--ansi');
        $options = json_encode([
            'username'         => 'LARAVEL',
            'hostname'         => php_uname('n'),
            'os'               => PHP_OS,
            'basePath'         => $app->basePath(),
            'environment'      => $app->environment(),
            'version'          => $app->version(),
            'endPoint'         => action('\\'.static::class.'@endPoint'),
            'helpInfo'         => $this->consoleKernel->output(),
            'interpreters'     => [
                'mysql'          => 'mysql',
                'artisan tinker' => 'tinker',
                'tinker'         => 'tinker',
            ],
            'confirmToProceed' => [
                'artisan' => [
                    'migrate',
                    'migrate:install',
                    'migrate:refresh',
                    'migrate:reset',
                    'migrate:rollback',
                    'db:seed',
                ],
            ],
        ]);

        $resourcePath = __DIR__.'/../../../public/';
        $resources = [];
        $resources['style'] = $filesystem->get($resourcePath.'css/app.css');
        $resources['app'] = $filesystem->get($resourcePath.'js/app.js');

        if ($panel === true) {
            $resources['jquery'] = $filesystem->get($resourcePath.'js/jquery.min.js');
            $resources['mousewheel'] = $filesystem->get($resourcePath.'js/jquery.mousewheel.min.js');
            $resources['terminal'] = $filesystem->get($resourcePath.'js/terminal.js');

            return view('terminal::panel', compact('options', 'resources'));
        }

        $resources['plugins'] = $filesystem->get($resourcePath.'js/plugins.js');

        return view('terminal::index', compact('options', 'resources'));
    }

    /**
     * rpc response.
     *
     * @param \Illuminate\Http\Request           $request
     *
     * @return mixed
     */
    public function endPoint(Request $request)
    {
        $cmd = $request->get('cmd');
        $command = array_get($cmd, 'command');
        $status = $this->consoleKernel->call($command);

        return response()->json([
            'jsonrpc' => $request->get('jsonrpc'),
            'id'      => $request->get('id'),
            'result'  => $this->consoleKernel->output(),
            'error'   => $status,
        ]);
    }
}
