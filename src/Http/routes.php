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

get('/', [
    'as' => 'index',
    'uses' => 'TerminalController@index',
]);

post('/mysql', [
    'as' => 'mysql',
    'uses' => 'TerminalController@mysql',
]);

post('/tinker', [
    'as' => 'tinker',
    'uses' => 'TerminalController@tinker',
]);

post('/artisan', [
    'as' => 'artisan',
    'uses' => 'TerminalController@artisan',
]);
