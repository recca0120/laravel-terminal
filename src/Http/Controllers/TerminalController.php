<?php

namespace Recca0120\Terminal\Http\Controllers;

use Application;
use Artisan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TerminalController extends Controller
{
    public function index()
    {
        $environment = app()->environment();

        return view('terminal::index', compact('environment'));
    }

    public function artisan(Request $request)
    {
        return response()->stream(function () use ($request) {
            // set_time_limit(30);
            $result = null;
            $error = null;

            $id = $request->input('id');
            $method = $request->input('method');

            if (empty($method) === true || starts_with($method, '--')) {
                $method = 'list';
            } else {
                $temp = array_map(function ($item) {
                    if (starts_with($item, '--') && strpos($item, '=') === false) {
                        $item .= '=default';
                    }

                    return $item;
                }, $request->input('params', []));
                $params = [];
                foreach ($temp as $tmp) {
                    $param = explode('=', $tmp);
                    $params[array_get($param, 0)] = array_get($param, 1, '');
                }
            }

            try {
                $exitCode = Artisan::call($method, $params);
                $result = Artisan::output();
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
