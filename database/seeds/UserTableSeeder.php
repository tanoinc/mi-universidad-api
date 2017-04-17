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
        $hash = 'abc123'; //sha1(random_bytes(8).'admin'); // Comentado para debug
        $user = User::create([
            'name' => 'Admin',
            'surnamename' => 'Admin',
            'username' => 'admin@localhost', 
            'hash_id' => $hash, 
            'email' => 'admin@localhost',
            'password' => password_hash(substr($hash, 0, 10), PASSWORD_DEFAULT),
        ]);
        $app = Application::all()->first();
        $user->applications()->attach($app, ['granted_privilege_version' => $app->privilege_version, 'external_id' => $user->id]);
    }

}
