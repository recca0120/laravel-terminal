<?php

namespace Recca0120\Terminal\Http\Controllers;

use Application;
use Artisan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class TerminalController extends Controller
{
    public function index()
    {
        $environment = app()->environment();

        return view('terminal::index', compact('environment'));
    }

    public function tinker(Request $request)
    {
        $result = null;
        $error = null;

        $id = $request->input('id');
        $command = $request->input('method');
        $parameters = $request->input('params');
        $command .= ' '.implode(' ', $parameters);

        try {
            ob_start();
            @eval('$result = '.$command);
            var_dump($result);
            $result = ob_get_contents();
            ob_end_clean();
        } catch (Exception $e) {
            $result = false;
            $error = $e->getMessage();
        }

        return response()->json([
            'jsonrpc' => '2.0',
            'result' => $result,
            // 'result' => $command,
            'id' => $id,
            'error' => $error,
        ]);
    }

    public function artisan(Request $request)
    {
        return response()->stream(function () use ($request) {

            // set_time_limit(30);
            $result = null;
            $error = null;

            $id = $request->input('id');
            $command = $request->input('method');

            $temp = array_map(function ($item) {
                if (starts_with($item, '--') && strpos($item, '=') === false) {
                    $item .= '=default';
                }

                return $item;
            }, $request->input('params', []));
            $parameters = [];
            foreach ($temp as $tmp) {
                $explodeTmp = explode('=', $tmp);
                $parameters[array_get($explodeTmp, 0)] = array_get($explodeTmp, 1, '');
            }

            try {
                $parameters['command'] = $command;
                $lastOutput = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true, new OutputFormatter());
                $exitCode = Artisan::handle(new ArrayInput($parameters), $lastOutput);
                $result = $lastOutput->fetch();
                // exit;
                // $exitCode = Artisan::call($command, $parameters);
                // $result = Artisan::output();
            } catch (Exception $e) {
                $result = false;
                $error = $e->getMessage();
            }

            echo json_encode([
                'jsonrpc' => '2.0',
                'result' => $result,
                'id' => $id,
                'error' => $error,
            ]);
        }, 200, [
            'content-type' => 'application/json',
        ]);
    }
}
