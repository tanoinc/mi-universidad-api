<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Application;
use App\Privilege;
/**
 * The Application table seeder
 *
 * @author tanoinc
 */
class ApplicationTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $app = Application::create([
            'name' => env('MOBILE_APP_NAME'),
            'description' => env('MOBILE_APP_NAME').' mobile app',
            'api_key'  => sha1(random_bytes(8).microtime()),
            'api_secret'  => sha1(random_bytes(8).microtime()),
            'privilege_version' => 1,
        ]);
        $all_privileges = Privilege::all();
        $app->privileges()->attach($all_privileges);
        
        $app = Application::create([
            'name' => "auth-test-app",
            'description' => "Auth mobile app",
            'api_key' => sha1(random_bytes(8).microtime()),
            'api_secret' => sha1(random_bytes(8).microtime()),
            'privilege_version' => 1,
            'auth_required' => true,
            'auth_callback_url' => 'http://2770281b.ngrok.io/mi_universidad_conectar',
        ]);
        $all_privileges = Privilege::all();
        $app->privileges()->attach($all_privileges);
    }

}
