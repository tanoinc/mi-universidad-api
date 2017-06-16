<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the "mi-universidad" user in an application.
 *
 * @author tanoinc
 */
class UserApplication extends Model
{

    protected $table = 'user_application';
    protected $fillable = [
        'external_id', 'granted_privilege_version'
    ];
    protected $hidden = [
        'id', 'deleted_at', 'created_at', 'subscription_token', 'application_id', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function application()
    {
        return $this->belongsTo('App\Application');
    }
    
    public function scopeFindByApplicationAndExternalId($query, $application_id, $external_id)
    {
        $query->where('application_id', '=', $application_id)->whereNotNull('granted_privilege_version');
        if (is_array($external_id)) {
            $query->whereIn('external_id', $external_id);
        } else {
            $query->where('external_id', '=', $external_id);
        }
        
        return $query;
    }
    
    public function scopeFindForSubscription($query, \App\Application $application, \App\User $user, $token) {
        $query->where('application_id', '=', $application->id)
                ->whereNull('granted_privilege_version')
                ->where('subscription_token', $token)
                ->where('user_id', $user->id);
        
        return $query;
    }
    
    public function generateSubscriptionToken() {
        $this->granted_privilege_version = null;
        return $this->subscription_token = sha1(random_bytes(8).microtime());
    }
    
    public function grant(Application $app) {
        $this->subscription_token = null;
        $this->granted_privilege_version = $app->privilege_version;
    }
    
    
}
