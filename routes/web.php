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
    return "Hola!";
});

// Public
$app->group(['prefix' => 'api/v1','namespace' => '\App\Http\Controllers'], function($app)
{
    $app->get('config/init','ConfigurationController@initialConfig');
    
    // User
    $app->post('user','UserController@createUser');
});

// Server-side apps (api-key + signature)
$app->group(['prefix' => 'api/v1','namespace' => '\App\Http\Controllers','middleware'=>['auth_api_key','check_privileges']], function($app)
{
    // Newsfeed
    $app->post('newsfeed','NewsfeedController@createNewsfeed');
    $app->get('newsfeed/user/{user_hash_id}','NewsfeedController@getFromUserHashId');    
    $app->get('newsfeed','NewsfeedController@index');

    // Application
    $app->get('application','ApplicationController@index');
    $app->get('application/{id}','ApplicationController@getById');
    $app->post('application','ApplicationController@createApplication');
    $app->put('application/{id}','ApplicationController@updateApplication');
    $app->delete('application/{id}','ApplicationController@deleteApplication');

    // Application privileges
    $app->get('application/{id}/granted_privileges','ApplicationController@getApplicationGrantedPrivileges');
});

// Mobile app (OAuth2)
$app->group(['prefix' => 'mobile/api/v1','namespace' => '\App\Http\Controllers','middleware'=>['auth']], function($app)
{
    // Applications
    $app->get('application','ApplicationController@getFromAuthenticatedUser');
    $app->get('application/available','ApplicationController@getAvailable');
    
    // Newsfeed
    $app->get('newsfeed','NewsfeedController@getFromAuthenticatedUser');
    
    // Subscriptions
    $app->post('context/subscription','SubscriptionController@subscribeUser');
    $app->delete('context/subscription/{application_name}/{context_name}','SubscriptionController@unsubscribeUser');
    $app->get('context/subscriptions','SubscriptionController@getFromAuthenticatedUser');
    $app->get('context/subscriptions/{application_name}','SubscriptionController@getByAppNameFromAuthenticatedUser');
    
    // Contexts
    $app->get('contexts/{application_name}','ContextController@getByApplication');    
    
    // User
    $app->get('user','UserController@getFromAuthenticatedUser');    
});