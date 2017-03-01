<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Application;

/**
 * The Privilege table seeder
 *
 * @author tanoinc
 */
class UserTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hash = sha1(random_bytes(8).'admin');
        $user = User::create([
            'username' => 'admin', 
            'hash_id' => $hash, 
            'email' => '',
            'password' => password_hash(substr($hash, 0, 10), PASSWORD_DEFAULT),
        ]);
        $app = Application::all()->first();
        $user->applications()->attach($app, ['granted_privilege_version' => $app->privilege_version]);
    }

}
