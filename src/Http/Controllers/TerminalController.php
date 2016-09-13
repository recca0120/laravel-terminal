<?php

namespace Recca0120\Terminal\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Str;
use Recca0120\Terminal\Kernel;

class TerminalController extends Controller
{
    /**
     * $kernel.
     *
     * @var \Recca0120\Terminal\Kernel
     */
    protected $kernel;

    /**
     * $app.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * $request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * $session.
     *
     * @var \Illuminate\Session\SessionInterface
     */
    protected $session;

    /**
     * __construct.
     *
     * @method __construct
     *
     * @param \Recca0120\Terminal\Kernel                   $kernel
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Session\SessionManager           $sessionManager
     * @param \Illuminate\Http\Request                     $request
     */
    public function __construct(
        Kernel $kernel,
        Application $app,
        SessionManager $sessionManager,
        Request $request
    ) {
        $this->consoleKernel = $kernel;
        $this->app = $app;
        $this->request = $request;
        $this->session = $sessionManager->driver();
    }

    /**
     * index.
     *
     * @param \Illuminate\Contracts\Response\Factory     $responseFactory
     * @param \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     * @param string                                     $view
     *
     * @return mixed
     */
    public function index(ResponseFactory $responseFactory, UrlGenerator $urlGenerator, $view = 'index')
    {
        $this->consoleKernel->call('--ansi');
        $options = json_encode([
            'csrfToken' => $this->session->token(),
            'username' => 'LARAVEL',
            'hostname' => php_uname('n'),
            'os' => PHP_OS,
            'basePath' => $this->app->basePath(),
            'environment' => $this->app->environment(),
            'version' => $this->app->version(),
            'endpoint' => $urlGenerator->action('\\'.static::class.'@endpoint'),
            'helpInfo' => $this->consoleKernel->output(),
            'interpreters' => [
                'mysql' => 'mysql',
                'artisan tinker' => 'tinker',
                'tinker' => 'tinker',
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
        $id = ($view === 'panel') ? Str::random(30) : null;

        return $responseFactory->view('terminal::'.$view, compact('options', 'resources', 'id'));
    }

    /**
     * rpc response.
     *
     * @param \Illuminate\Contracts\Response\Factory $responseFactory
     *
     * @return mixed
     */
    public function endpoint(ResponseFactory $responseFactory)
    {
        $command = $this->request->get('command');
        $status = $this->consoleKernel->call($command);

        return $responseFactory->json([
            'jsonrpc' => $this->request->get('jsonrpc'),
            'id' => $this->request->get('id'),
            'result' => $this->consoleKernel->output(),
            'error' => $status,
        ]);
    }

    /**
     * media.
     *
     * @param \Illuminate\Filesystem\Filesystem             $filesystem
     * @param \Illuminate\Contracts\Routing\ResponseFactory $request
     * @param string                                        $file
     *
     * @return \Illuminate\Http\Response
     */
    public function media(
        Filesystem $filesystem,
        ResponseFactory $responseFactory,
        $file
    ) {
        $filename = __DIR__.'/../../../public/'.$file;
        $mimeType = strpos($filename, '.css') !== false ? 'text/css' : 'application/javascript';
        $lastModified = $filesystem->lastModified($filename);
        $eTag = sha1_file($filename);
        $headers = [
            'content-type' => $mimeType,
            'last-modified' => date('D, d M Y H:i:s ', $lastModified).'GMT',
        ];

        if (@strtotime($this->request->server('HTTP_IF_MODIFIED_SINCE')) === $lastModified ||
            trim($this->request->server('HTTP_IF_NONE_MATCH'), '"') === $eTag
        ) {
            $response = $responseFactory->make(null, 304, $headers);
        } else {
            $response = $responseFactory->stream(function () use ($filename) {
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
