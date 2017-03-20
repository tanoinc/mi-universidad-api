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
            'name' => 'mi-universidad-mobile',
            'description' => 'Mi-universidad Mobile app',
            'api_key'  => sha1(random_bytes(8).microtime()),
            'api_secret'  => sha1(random_bytes(8).microtime()),
            'privilege_version' => 1,
        ]);
        $all_privileges = Privilege::all();
        $app->privileges()->attach($all_privileges);
    }

}
