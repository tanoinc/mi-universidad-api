<?php

namespace App\Http\Controllers;

class ConfigurationController extends Controller
{
    public function initialConfig()
    {
        $config = [ 
            'client_id' => env('OAUTH_CLIENT_ID'), 
            'client_secret' => env('OAUTH_CLIENT_SECRET'),
            'contact_email' => env('CONTACT_EMAIL', 'test@test'),
            'contact_subject' => env('CONTACT_SUBJECT', '[miuniversidad] contact')
        ];
        
        return response()->json($config);
    }
    
    public function serviceStatus()
    {
        return response()->json([
            'http' => true,
            'db' => $this->getStatusDb(),
            'app' => $this->getStatusApp(),
        ]);
    }
    
    protected function getStatusDb()
    {
        try {
            $db = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
            return (env('DB_DATABASE') == $db);
        } catch (Exception $e) {
            return false;
        }
    }
    
    protected function getStatusApp()
    {
        try {
            $app = \App\Application::findByName(env('MOBILE_APP_NAME'))->first();
            return ($app and $app->deleted_at == null);
        } catch (Exception $e) {
            return false;
        }
    }
}
