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
    
    $app->get('config/service_status','ConfigurationController@serviceStatus');
    
    // User
    $app->post('user','UserController@createUser');
});

// Server-side apps (api-key + signature)
$app->group(['prefix' => 'api/v1','namespace' => '\App\Http\Controllers','middleware'=>['auth_api_key','check_privileges']], function($app)
{
    // Newsfeed
    $app->post('newsfeed','NewsfeedController@create');
    $app->get('newsfeed/user/{user_hash_id}','NewsfeedController@getFromUserHashId');    
    $app->get('newsfeed','NewsfeedController@index');

    // Calendar events
    $app->post('calendar_event','CalendarEventController@create');
    $app->get('calendar_event/user/{user_hash_id}','CalendarEventController@getFromUserHashId');    
    $app->get('calendar_event','CalendarEventController@index');
    
    // Application
    $app->get('application','ApplicationController@index');
    $app->get('application/{id}','ApplicationController@getById');
    $app->post('application','ApplicationController@createApplication');
    //$app->put('application/{id}','ApplicationController@updateApplication');
    //$app->delete('application/{id}','ApplicationController@deleteApplication');

    // Application privileges
    $app->get('application/{id}/granted_privileges','ApplicationController@getApplicationGrantedPrivileges');
    
    // Content: Generic content CRUD
    $app->get('content','ContentController@index');
    $app->post('content/{content_type}','ContentController@create');
    $app->put('content/{content_id}','ContentController@update');
    $app->delete('content/{content_id}','ContentController@delete');    
});

// Mobile app (OAuth2)
$app->group(['prefix' => 'mobile/api/v1','namespace' => '\App\Http\Controllers','middleware'=>['auth']], function($app)
{
    // Applications
    $app->get('application','ApplicationController@getFromAuthenticatedUser');
    $app->get('application/available','ApplicationController@getAvailable');

    // Notifications
    $app->get('notification','NotificationController@getFromAuthenticatedUser');
    
    // Newsfeed
    $app->get('newsfeed','NewsfeedController@getFromAuthenticatedUser');

    // Calendar events
    $app->get('calendar_event','CalendarEventController@getFromAuthenticatedUser');
    
    // Subscriptions
    $app->post('context/subscription','SubscriptionController@subscribeUser');
    $app->delete('context/subscription/{application_name}/{context_name}','SubscriptionController@unsubscribeUser');
    $app->get('context/subscriptions','SubscriptionController@getFromAuthenticatedUser');
    $app->get('context/subscriptions/{application_name}','SubscriptionController@getByAppNameFromAuthenticatedUser');
    
    // Contexts
    $app->get('contexts/{application_name}','ContextController@getByApplication');    
    
    // User
    $app->get('user','UserController@getFromAuthenticatedUser');
    $app->post('user/push_token','UserController@registerPushToken');
    $app->delete('user/push_token/{type}/{token}','UserController@unregisterPushToken');
});