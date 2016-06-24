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

Route::get('/{view?}', 'TerminalController@index');
Route::post('/endpoint', 'TerminalController@endPoint');
Route::get('/media/{file}', 'TerminalController@media')
    ->where(['file' => '.+']);
