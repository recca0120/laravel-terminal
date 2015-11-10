<?php

namespace Recca0120\Terminal\Http\Controllers;

use Artisan;
use Illuminate\Http\Request;
use InvalidArgumentException;

class TerminalController extends Controller
{
    public function index()
    {
        return view('terminal::index');
    }

    public function artisan(Request $request)
    {
        return response()->stream(function () use ($request) {
            set_time_limit(0);
            $result = null;
            $error = null;

            $id = $request->input('id');
            $method = $request->input('method');
            $params = $request->input('params');

            if (empty($method) === true) {
                $method = 'list';
            }

            try {
                $exitCode = Artisan::call($method, $params);
                $result = Artisan::output();
            } catch (InvalidArgumentException $e) {
                $result = $e->getMessage();
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
