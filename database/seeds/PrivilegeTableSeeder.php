<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Privilege;

/**
 * The Privilege table seeder
 *
 * @author tanoinc
 */
class PrivilegeTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $informations = ['newsfeed' => 'NewsfeedController', 'calendar_event' => 'CalendarEventController', 'content' => 'ContentController'];
        foreach ($informations as $model_table => $controller) {
            Privilege::create([
                'name' => $model_table.':post',
                'description' => 'To create a new item in the '.$model_table,
                'controller_action' => 'App\\Http\\Controllers\\'.$controller.'@create'
            ]);
            Privilege::create([
                'name' => $model_table.':put',
                'description' => 'To update an item from the '.$model_table,
                'controller_action' => 'App\\Http\\Controllers\\'.$controller.'@update',
            ]);
            Privilege::create([
                'name' => $model_table.':delete',
                'description' => 'To delete an item from the '.$model_table,
                'controller_action' => 'App\\Http\\Controllers\\'.$controller.'@delete',
            ]);
            Privilege::create([
                'name' => $model_table.':get',
                'description' => 'To get an item from the '.$model_table,
                'controller_action' => 'App\\Http\\Controllers\\'.$controller.'@get',
            ]);
            Privilege::create([
                'name' => $model_table.':send_notification',
                'description' => 'Send '.$model_table.' push notifictions to users mobile app',
                'controller_action' => 'App\\Http\\Controllers\\'.$controller.'@create'
            ]);
        }

        Privilege::create([
            'name' => 'application:get',
            'description' => 'Get current authenticated application data',
            'controller_action' => 'App\Http\Controllers\ApplicationController@index',
        ]);
        Privilege::create([
            'name' => 'content:list',
            'description' => 'Get the content list from the authenticated app',
            'controller_action' => 'App\Http\Controllers\ContentController@index',
        ]);
        Privilege::create([
            'name' => 'application:subscribe',
            'description' => 'Subscribe application to users',
            'controller_action' => 'App\Http\Controllers\ApplicationController@updateSubscription',
        ]);        
    }

}
