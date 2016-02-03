<?php

namespace Recca0120\Terminal\Http\Controllers;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Recca0120\Terminal\Console\Kernel as ConsoleKernel;

class TerminalController extends Controller
{
    /**
     * index.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Recca0120\Terminal\Console\Kernel           $consoleKernel
     * @param \Illuminate\Http\Request                     $request
     *
     * @return mixed
     */
    public function index(
        ApplicationContract $app,
        ConsoleKernel $consoleKernel,
        Request $request
    ) {
        $environment = $app->environment();
        $endPoint = action('\\'.static::class.'@rpcResponse');

        $consoleKernel->call('--ansi');
        $helpInfo = json_encode($consoleKernel->output());

        return view(
            'terminal::index',
            compact('environment', 'endPoint', 'helpInfo')
        );
    }

    /**
     * rpc response.
     *
     * @param \Recca0120\Terminal\Console\Kernel $consoleKernel
     * @param \Illuminate\Http\Request           $request
     *
     * @return mixed
     */
    public function rpcResponse(
        ConsoleKernel $consoleKernel,
        Request $request
    ) {
        $cmd = $request->get('cmd');
        $command = array_get($cmd, 'command');
        $status = $consoleKernel->call($command);

        return response()->json([
            'jsonrpc' => $request->get('jsonrpc'),
            'id'      => $request->get('id'),
            'result'  => $consoleKernel->output(),
            'error'   => $status,
        ]);
    }
}
