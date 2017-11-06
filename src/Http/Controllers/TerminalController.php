<?php

namespace Recca0120\Terminal\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Recca0120\Terminal\Kernel;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;
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
     * @param \Recca0120\Terminal\Kernel $kernel
     * @param string $view
     * @return \Illuminate\Http\Response
     */
    public function index(Kernel $kernel, $view = 'index')
    {
        $token = null;
        if ($this->request->hasSession() === true) {
            $token = $this->request->session()->token();
        }

        $kernel->call('list --ansi');
        $options = json_encode(array_merge($kernel->getConfig(), [
            'csrfToken' => $token,
            'helpInfo' => $kernel->output(),
        ]));
        $id = ($view === 'panel') ? Str::random(30) : null;

        return $this->responseFactory->view('terminal::'.$view, compact('options', 'id'));
    }

    /**
     * rpc response.
     *
     * @param \Recca0120\Terminal\Kernel $kernel
     * @return \Illuminate\Http\JsonResponse
     */
    public function endpoint(Kernel $kernel)
    {
        $error = $kernel->call(
            $this->request->get('command'),
            $this->request->get('parameters', [])
        );

        return $this->responseFactory->json([
            'jsonrpc' => $this->request->get('jsonrpc'),
            'id' => $this->request->get('id'),
            'result' => $kernel->output(),
            'error' => $error,
        ]);
    }

    /**
     * media.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param string $file
     * @return \Illuminate\Http\Response
     */
    public function media(Filesystem $files, $file)
    {
        $filename = __DIR__.'/../../../public/'.$file;
        $mimeType = strpos($filename, '.css') !== false ? 'text/css' : 'application/javascript';
        $lastModified = $files->lastModified($filename);
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
