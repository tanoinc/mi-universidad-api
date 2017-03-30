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
        Privilege::create([
            'name' => 'newsfeed:post',
            'description' => 'To create a new post in the newsfeed',
            'controller_action' => 'App\Http\Controllers\NewsfeedController@createNewsfeed'
        ]);
        Privilege::create([
            'name' => 'newsfeed:put',
            'description' => 'To update a post from the newsfeed',
            'controller_action' => 'App\Http\Controllers\NewsfeedController@updateNewsfeed',
        ]);
        Privilege::create([
            'name' => 'newsfeed:delete',
            'description' => 'To delete a post from the newsfeed',
            'controller_action' => 'App\Http\Controllers\NewsfeedController@deleteNewsfeed',
        ]);
        Privilege::create([
            'name' => 'newsfeed:get',
            'description' => 'To get a post from the newsfeed',
            'controller_action' => 'App\Http\Controllers\NewsfeedController@getNewsfeed',
        ]);         
    }

}
