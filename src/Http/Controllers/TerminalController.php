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

        if ($panel === true) {
            $style = $filesystem->get(public_path('vendor/terminal/css/app.css'));
            $jquery = $filesystem->get(public_path('vendor/terminal/js/jquery.min.js'));
            $mousewheel = $filesystem->get(public_path('vendor/terminal/js/jquery.mousewheel.min.js'));
            $terminal = $filesystem->get(public_path('vendor/terminal/js/terminal.js'));
            $script = $filesystem->get(public_path('vendor/terminal/js/app.js'));

            return view('terminal::panel', compact('options', 'style', 'jquery', 'mousewheel', 'terminal', 'script'));
        }

        return view('terminal::index', compact('options'));
    }

    /**
     * rpc response.
     *
     * @param \Illuminate\Http\Request $request
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
