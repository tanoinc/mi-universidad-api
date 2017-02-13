<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return "Hola Mundo! '/' ";
});

$app->get('/hola', function () use ($app) {
    return "Hola Mundo!";
});

$app->group(['prefix' => 'api/v1','namespace' => '\App\Http\Controllers'], function($app)
{
    $app->get('application','ApplicationController@index');
    $app->get('application/{id}','ApplicationController@getApplication');
    $app->post('application','ApplicationController@createApplication');
    $app->put('application/{id}','ApplicationController@updateApplication');
    $app->delete('application/{id}','ApplicationController@deleteApplication');    
});