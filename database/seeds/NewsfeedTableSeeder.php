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
use App\Application;

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
        $app = Application::all()->first();
        $admin = User::all()->first();
        $newsfeed = Newsfeed::create([
            'title' => 'Bienvenido',
            'content' => 'Esta es la primer noticia de tu Newsfeed personal!',
            'send_notification' => false,
            'application_id' => $app->id,
            'global' => 0,
        ]);
        $newsfeed->users()->attach($admin);
        $newsfeed = Newsfeed::create([
            'title' => 'Bienvenido nuevamente!',
            'content' => 'Esta es otra noticia de tu Newsfeed personal!',
            'send_notification' => false,
            'application_id' => $app->id,
            'global' => 0,
        ]);
        $newsfeed->users()->attach($admin);
        Newsfeed::create([
            'title' => sprintf('Bienvenido a "%s"', $app->name),
            'content' => sprintf('Esta es la primer noticia de tu Newsfeed de "%s"!', $app->name),
            'send_notification' => false,
            'application_id' => $app->id,
            'global' => 1,
        ]);
        Newsfeed::create([
            'title' => sprintf('Otra noticia de "%s"', $app->name),
            'content' => sprintf('Esta es otra noticia de tu Newsfeed de "%s"!', $app->name),
            'send_notification' => false,
            'application_id' => $app->id,
            'global' => 1,
        ]);
    }

}
