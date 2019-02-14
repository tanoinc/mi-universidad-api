<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Description of PushNotificationsServiceProvider
 *
 * @author lucianoc
 */
class PushNotificationsServiceProvider extends ServiceProvider
{
    protected $defer = true;
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Library\Generic\PushNotificationsInterface::class, function ($app) {
            return new \App\Library\PushNotificationsLaravelFCM();
        });
    }
}
