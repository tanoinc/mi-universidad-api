<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Newsfeed;
use App\User;

/**
 * The Example admin newsfeed table seeder
 *
 * @author tanoinc
 */
class NewsfeedTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::all()->first();
        $newsfeed = Newsfeed::create([
            'title' => 'Bienvenido',
            'content' => 'Esta es la primer noticia de tu Newsfeed!',
            'send_notification' => false,
        ]);
        $newsfeed->users()->attach($admin);
    }

}
