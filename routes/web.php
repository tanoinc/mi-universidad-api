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

$app->group(['prefix' => 'api/v1','namespace' => '\App\Http\Controllers','middleware'=>'auth_api_key'], function($app)
{
    // Application
    $app->get('application','ApplicationController@index');
    $app->get('application/{id}','ApplicationController@getApplication');
    $app->post('application','ApplicationController@createApplication');
    $app->put('application/{id}','ApplicationController@updateApplication');
    $app->delete('application/{id}','ApplicationController@deleteApplication');
    
    // Application privileges
    $app->get('application/{id}/granted_privileges','ApplicationController@getApplicationGrantedPrivileges');
    
    // Newsfeed
    $app->get('newsfeed','NewsfeedController@index');
    $app->post('newsfeed','NewsfeedController@createNewsfeed');
    $app->get('newsfeed/user/{user_hash_id}','NewsfeedController@getFromUser');
    
    // User
    $app->post('user','UserController@createUser');
});