<?php

namespace Recca0120\Terminal\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;
use Recca0120\Terminal\TerminalManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;

class TerminalController extends Controller
{
    /**
     * $request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * $responseFactory.
     *
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $responseFactory;

    /**
     * __construct.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $responseFactory
     */
    public function __construct(Request $request, ResponseFactory $responseFactory)
    {
        $this->request = $request;
        $this->responseFactory = $responseFactory;
    }

    /**
     * index.
     *
     * @param \Recca0120\Terminal\TerminalManager $terminalManger
     * @param string $view
     * @return \Illuminate\Http\Response
     */
    public function index(TerminalManager $terminalManger, $view = 'index')
    {
        $token = null;
        if ($this->request->hasSession() === true) {
            $token = $this->request->session()->token();
        }

        $terminalManger->call('list --ansi');
        $options = json_encode(array_merge($terminalManger->getConfig(), [
            'csrfToken' => $token,
            'helpInfo' => $terminalManger->output(),
        ]));
        $id = ($view === 'panel') ? Str::random(30) : null;

        return $this->responseFactory->view('terminal::'.$view, compact('options', 'id'));
    }

    /**
     * rpc response.
     *
     * @param \Recca0120\Terminal\TerminalManager $terminalManger
     * @return \Illuminate\Http\JsonResponse
     */
    public function endpoint(TerminalManager $terminalManger)
    {
        if ($this->request->hasSession() === true) {
            $session = $this->request->session();
            if ($session->isStarted() === true) {
                $session->save();
            }
        }

        $error = $terminalManger->call($this->request->get('command'));

        return $this->responseFactory->json([
            'jsonrpc' => $this->request->get('jsonrpc'),
            'id' => $this->request->get('id'),
            'result' => $terminalManger->output(),
            'error' => $error,
        ]);
    }

    /**
     * media.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param string $file
     * @return \Illuminate\Http\Response
     */
    public function media(Filesystem $filesystem, $file)
    {
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
            $response = $this->responseFactory->make(null, 304, $headers);
        } else {
            $response = $this->responseFactory->stream(function () use ($filename) {
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
