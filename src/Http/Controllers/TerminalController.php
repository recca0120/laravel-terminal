<?php

namespace Recca0120\Terminal\Http\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Recca0120\Terminal\Console\Kernel;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

class TerminalController extends Controller
{
    private $request;

    public function index(Application $app)
    {
        $environment = $app->environment();
        $endPoint = action('\\'.static::class.'@rpcResponse');

        return view('terminal::index2', compact('environment', 'endPoint'));
    }

    public function rpcResponse(Application $app, Request $request)
    {
        $cmd = $request->get('cmd');
        $argv = array_merge(['artisan', array_get($cmd, 'name')], array_get($cmd, 'args', []));

        $input = new ArgvInput($argv);
        $output = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true, new OutputFormatter(true));
        $kernel = $app->make(Kernel::class);
        $status = $kernel->handle($input, $output);
        $kernel->terminate($input, $status);
        $result = $output->fetch();

        return response()->json([
            'jsonrpc' => $request->get('jsonrpc'),
            'id' => $request->get('id'),
            'result' => $result."\n",
            'error' => $status,
        ]);
    }
}
