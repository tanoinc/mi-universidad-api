<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Illuminate\Database\Seeder;
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
        $this->createApp(env('MOBILE_APP_NAME'));
        $this->createApp('auth-test-app', true, 'http://localhost:8803/mi_universidad_conectar');
    }

    protected function createApp($app_name, $auth_required = false, $callback_url = null)
    {
        return Application::create([
            'name' => $app_name,
            'description' => $app_name . ' mobile app',
            'privilege_version' => 1,
            'auth_required' => $auth_required,
            'auth_callback_url' => $callback_url,
        ]);
    }

}
