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

$router->get('/', function () use ($router) {
    return sprintf("Hola %s!", env('MOBILE_APP_NAME'));
});

// Public
$router->group(['prefix' => 'v1','namespace' => '\App\Http\Controllers'], function($router)
{
    $router->get('config/init','ConfigurationController@initialConfig');

    $router->get('config/service_status','ConfigurationController@serviceStatus');
    $router->get('config/version/{version}/compatibility','ConfigurationController@versionCompatibility');

    // User
    $router->post('user','UserController@createUser');
    $router->post('user/password','UserController@forgotPassword');
    $router->put('user/password','UserController@forgotPasswordReset');
    $router->put('user/confirmation','UserController@confirmUser');
    
    //facebook login
    //$router->post('auth/facebook','ExternalAuthController@facebook');
    $router->post('auth/facebook','ExternalAccessTokenController@issueToken');

});

// Server-side apps (api-key + signature)
$router->group(['prefix' => 'v1','namespace' => '\App\Http\Controllers','middleware'=>['auth_api_key','check_privileges']], function($router)
{
    // Newsfeed
    $router->post('newsfeed','NewsfeedController@create');
    //$router->get('newsfeed/user/{user_hash_id}','NewsfeedController@getFromUserHashId');  //@TODO: Filtrar por aplicación
    //$router->get('newsfeed','NewsfeedController@index'); //@TODO: Filtrar por aplicación

    // Calendar events
    $router->post('calendar_event','CalendarEventController@create');
    $router->delete('calendar_event/{id}','CalendarEventController@delete');
    //$router->get('calendar_event/user/{user_hash_id}','CalendarEventController@getFromUserHashId'); //@TODO: Filtrar por aplicación
    //$router->get('calendar_event','CalendarEventController@index'); //@TODO: Filtrar por aplicación

    // Attendance
    $router->post('attendance','AttendanceController@create');
    $router->delete('attendance/{id}','AttendanceController@delete');    
    
    // Application
    $router->get('application','ApplicationController@index');
    $router->get('application/{id}','ApplicationController@getById');
    $router->post('application','ApplicationController@createApplication');
    $router->put('application/subscription/{id}/{token}','ApplicationController@updateSubscription');
    //$router->put('application/{id}','ApplicationController@updateApplication');
    //$router->delete('application/{id}','ApplicationController@deleteApplication');

    // Application privileges
    $router->get('privileges/granted/application/{id}','ApplicationController@getApplicationGrantedPrivileges');
    $router->get('privileges/granted','ApplicationController@getGrantedPrivileges');

    // Content: Generic content CRUD
    $router->get('content','ContentController@index');
    $router->get('content/{id}','ContentController@get');
    $router->post('content/{content_type}','ContentController@create');
    $router->put('content/{id}','ContentController@update');
    $router->delete('content/{id}','ContentController@delete');

    //Geolocation
    $router->get('geolocation/user/{user_external_id}','GeolocationController@getFromUserHashId');
    $router->post('geolocation/users','GeolocationController@getFromUsers');
});

// Mobile app (OAuth2)
$router->group(['prefix' => 'mobile/v1','namespace' => '\App\Http\Controllers','middleware'=>['auth']], function($router)
{
    // Applications
    $router->get('application','ApplicationController@getFromAuthenticatedUser');
    $router->get('application/available','ApplicationController@getAvailable');
    $router->post('application/subscription', 'ApplicationController@subscribe');
    $router->delete('application/subscription/{application_name}', 'ApplicationController@unsubscribe');
    $router->get('application/content','ContentController@getFromAuthenticatedUser');
    $router->get('application/content/{application_name}','ContentController@getFromApplication');

    // Notifications
    $router->get('notification','NotificationController@getFromAuthenticatedUser');
    $router->post('notification/read','NotificationController@read');

    // Newsfeed
    $router->get('newsfeed','NewsfeedController@getFromAuthenticatedUser');

    // Calendar events
    $router->get('calendar_event','CalendarEventController@getFromAuthenticatedUser');
    $router->get('calendar_event/past','CalendarEventController@getPast');
    $router->get('calendar_event/future','CalendarEventController@getFuture');
    $router->get('calendar_event/between_dates/{start_date}/{end_date}','CalendarEventController@getBetweenDates');
    
    //Attendance
    $router->get('attendance','AttendanceController@getFromAuthenticatedUser');
    $router->get('attendance/future','AttendanceController@getFuture');
    $router->get('attendance/now','AttendanceController@getNow');
    $router->put('attendance/{attendance_id}/status/present','AttendanceController@changeStatusPresent');

    // Subscriptions
    $router->post('context/subscription','SubscriptionController@subscribeUser');
    $router->delete('context/subscription/{application_name}/{context_name}','SubscriptionController@unsubscribeUser');
    $router->get('context/subscriptions','SubscriptionController@getFromAuthenticatedUser');
    $router->get('context/subscriptions/{application_name}','SubscriptionController@getByAppNameFromAuthenticatedUser');

    // Contexts
    $router->get('contexts/{application_name}','ContextController@getByApplication');

    // User
    $router->get('user','UserController@getFromAuthenticatedUser');
    $router->post('user/push_token','UserController@registerPushToken');
    $router->delete('user/push_token/{type}/{token}','UserController@unregisterPushToken');
    $router->post('user/location','UserController@registerLocation');
    $router->put('user/password','UserController@passwordChange');

    // Contents
    $router->get('content','ContentController@getFromAuthenticatedUser');
    $router->get('content/data_url/{content_id}','ContentController@getFromUrl');
    $router->post('content/data_url/{content_id}','ContentController@getFromUrl');
});
