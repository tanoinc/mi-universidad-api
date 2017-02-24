<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Application;

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
        Application::create([
            'application_hash_id' => sha1(random_bytes(8).'mi-universidad-mobile'),
            'name' => 'mi-universidad-mobile',
            'description' => 'Mi-universidad Mobile app',
            'token_secret'  => sha1(random_bytes(8).microtime()),
        ]);
    }

}
