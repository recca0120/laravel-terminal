<?php

namespace Recca0120\Terminal\Http\Controllers;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Session\SessionManager;
use Recca0120\Terminal\Console\Kernel as ConsoleKernel;

class TerminalController extends Controller
{
    /**
     * $consoleKernel.
     *
     * @var \Recca0120\Terminal\Console\Kernel
     */
    protected $consoleKernel;

    /**
     * __construct.
     *
     * @method __construct
     *
     * @param ConsoleKernel $consoleKernel
     */
    public function __construct(ConsoleKernel $consoleKernel)
    {
        $this->consoleKernel = $consoleKernel;
    }

    /**
     * index.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Session\SessionManager           $sessionManager
     * @param \Illuminate\Http\Request                     $request
     * @param string                                       $view
     *
     * @return mixed
     */
    public function index(
        ApplicationContract $app,
        SessionManager $sessionManager,
        Request $request,
        $view = 'index'
    ) {
        $session = $sessionManager->driver();
        if ($session->isStarted() === false) {
            $session->setId($request->cookies->get($session->getName()));
            $session->setRequestOnHandler($request);
            $session->start();
        }
        $this->consoleKernel->call('--ansi');
        $options = json_encode([
            'csrfToken'        => $session->token(),
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

        return view('terminal::'.$view, compact('options', 'resources'));
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

    /**
     * media.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param \Illuminate\Http\Request          $request
     * @param string                            $file
     *
     * @return \Illuminate\Http\Response
     */
    public function media(Filesystem $filesystem, Request $request, $file)
    {
        $filename = __DIR__.'/../../../public/'.$file;
        $mimeType = strpos($filename, '.css') !== false ? 'text/css' : 'application/javascript';
        $lastModified = $filesystem->lastModified($filename);
        $eTag = sha1_file($filename);
        $headers = [
            'content-type'  => $mimeType,
            'last-modified' => date('D, d M Y H:i:s ', $lastModified).'GMT',
        ];

        if (@strtotime($request->server('HTTP_IF_MODIFIED_SINCE')) === $lastModified ||
            trim($request->server('HTTP_IF_NONE_MATCH'), '"') === $eTag
        ) {
            $response = response(null, 304, $headers);
        } else {
            $response = response()->stream(function () use ($filename) {
                $out = fopen('php://output', 'wb');
                $file = fopen($filename, 'rb');
                stream_copy_to_stream($file, $out, filesize($filename));
                fclose($out);
                fclose($file);
            }, 200, $headers);
        }

        return $response->setEtag($eTag);
    }
}
