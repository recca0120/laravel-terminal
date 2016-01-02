<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$middleware = [];
if (method_exists(app(), 'bindShared') === false) {
    $middleware = array_merge(['web'], $middleware);
}

Route::group(['middleware' => $middleware], function () {
    // Route::controller('/', 'TerminalController');
    Route::get('/', 'TerminalController@index');
    Route::post('/response', 'TerminalController@rpcResponse');
});
