<?php

namespace App\Http\Controllers;

class ConfigurationController extends Controller
{
    public function initialConfig()
    {
        $config = [ 'client_id' => env('OAUTH_CLIENT_ID'), 'client_secret' => env('OAUTH_CLIENT_SECRET')];
        
        return response()->json($config);
    }
}
