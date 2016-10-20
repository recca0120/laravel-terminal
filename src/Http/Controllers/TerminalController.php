<?php

namespace Recca0120\Terminal\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Recca0120\Terminal\Kernel;

class TerminalController extends Controller
{
    /**
     * index.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Recca0120\Terminal\Kernel                   $kernel
     * @param \Illuminate\Http\Request                     $request
     * @param \Illuminate\Contracts\Response\Factory       $responseFactory
     * @param \Illuminate\Contracts\Routing\UrlGenerator   $urlGenerator
     * @param string                                       $view
     *
     * @return mixed
     */
    public function index(Application $app, Kernel $kernel, Request $request, ResponseFactory $responseFactory, UrlGenerator $urlGenerator, $view = 'index')
    {
        $kernel->call('--ansi');
        $csrfToken = null;
        if ($request->hasSession() === true) {
            $csrfToken = $request->session()->token();
        }
        $options = json_encode([
            'csrfToken' => $csrfToken,
            'username' => 'LARAVEL',
            'hostname' => php_uname('n'),
            'os' => PHP_OS,
            'basePath' => $app->basePath(),
            'environment' => $app->environment(),
            'version' => $app->version(),
            'endpoint' => $urlGenerator->action('\\'.static::class.'@endpoint'),
            'helpInfo' => $kernel->output(),
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
     * @param \Recca0120\Terminal\Kernel             $kernel
     * @param \Illuminate\Http\Request               $request
     * @param \Illuminate\Contracts\Response\Factory $responseFactory
     *
     * @return mixed
     */
    public function endpoint(Kernel $kernel, Request $request, ResponseFactory $responseFactory)
    {
        if ($request->hasSession() === true) {
            $session = $request->session();
            if ($session->isStarted() === true) {
                $session->save();
            }
        }

        $command = $request->get('command');
        $status = $kernel->call($command);

        return $responseFactory->json([
            'jsonrpc' => $request->get('jsonrpc'),
            'id' => $request->get('id'),
            'result' => $kernel->output(),
            'error' => $status,
        ]);
    }

    /**
     * media.
     *
     * @param \Illuminate\Http\Request                      $request
     * @param \Illuminate\Filesystem\Filesystem             $filesystem
     * @param \Illuminate\Contracts\Routing\ResponseFactory $request
     * @param string                                        $file
     *
     * @return \Illuminate\Http\Response
     */
    public function media(
        Request $request,
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

        if (@strtotime($request->server('HTTP_IF_MODIFIED_SINCE')) === $lastModified ||
            trim($request->server('HTTP_IF_NONE_MATCH'), '"') === $eTag
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
