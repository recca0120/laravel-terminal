<?php

namespace Recca0120\Terminal\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Recca0120\Terminal\Console\Kernel;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

class TerminalController extends Controller
{
    /**
     * index.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return mixed
     */
    public function index(Application $app, Kernel $kernel, Request $request)
    {
        $environment = $app->environment();
        $endPoint = action('\\'.static::class.'@rpcResponse');
        $defaultResponse = $this->rpcResponse($kernel, $request)->content();

        return view('terminal::index', compact('environment', 'endPoint', 'defaultResponse'));
    }

    /**
     * rpc response.
     *
     * @param \Recca0120\Terminal\Console\Kernel $kernel
     * @param \Illuminate\Http\Request           $request
     *
     * @return mixed
     */
    public function rpcResponse(Kernel $kernel, Request $request)
    {
        $cmd = $request->get('cmd');
        $argv = array_merge(['artisan', array_get($cmd, 'name')], array_get($cmd, 'args', []));
        $input = new ArgvInput($argv);
        $output = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true, new OutputFormatter(true));
        $status = $kernel->handle($input, $output);

        return response()->json([
            'jsonrpc' => $request->get('jsonrpc'),
            'id'      => $request->get('id'),
            'result'  => $output->fetch(),
            'error'   => $status,
        ]);
    }
}
