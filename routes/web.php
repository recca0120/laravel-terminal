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

Route::get('/{view?}', [
    'as' => 'index',
    'uses' => 'TerminalController@index',
]);

Route::post('/endpoint', [
    'as' => 'endpoint',
    'uses' => 'TerminalController@endpoint',
]);

Route::get('/media/{file}', [
    'as' => 'media',
    'uses' => 'TerminalController@media',
])->where(['file' => '.+']);
