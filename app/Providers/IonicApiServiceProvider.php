<?php
namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Description of IonicApiServiceProvider
 *
 * @author lucianoc
 */
class IonicApiServiceProvider extends ServiceProvider
{
    protected $defer = true;
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Library\IonicApiV2::class, function ($app) {
            return new \App\Library\IonicApiV2(env('IONIC_API_ENDPOINT_URL'), env('IONIC_API_PUSH_PROFILE'), env('IONIC_API_TOKEN'));
        });
    }
}
