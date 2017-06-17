<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of Application
 *
 * @author tanoinc
 */
class Application extends Model
{

    protected $table = 'application';
    protected $fillable = [
        'name', 'description',
    ];
    protected $hidden = [
        'id', 'api_key', 'deleted_at', 'updated_at', 'api_secret', 'pivot', 'auth_callback_url'
    ];

    public function privileges()
    {
        return $this->belongsToMany('App\Privilege');
    }

    public function granted_privileges()
    {
        return $this->privileges()->wherePivot('version', '=', $this->privilege_version);
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_application');
    }
    
    public function subscribed_users()
    {
        return User::addApplicationSubscriptionCondition($this->users());
    }
    
    public function granted_action($controller_action_name)
    {
        return $this->granted_privileges()->where('privilege.controller_action', '=', $controller_action_name);
    }

    public function granted_privilege($privilege_name)
    {
        return $this->granted_privileges()->where('privilege.name', '=', $privilege_name);
    }

    public function has_granted_action($controller_action_name)
    {
        return $this->granted_action($controller_action_name)->first();
    }

    public function has_granted_privilege($privilege_name)
    {
        return $this->granted_privilege($privilege_name)->first();
    }

    public function newsfeeds()
    {
        return $this->hasMany('App\Newsfeed');
    }

    public function contexts()
    {
        return $this->hasMany('App\Context');
    }

    public function contents()
    {
        return $this->hasMany('App\Content');
    }    
    
    public function global_newsfeeds($union = null)
    {
        $q = $this->newsfeeds()->where('global', true)->orderBy('created_at', 'desc');
        if ($union) {
            $q->union($union);
        }
        return $q;
    }

    public function scopeFindByName($query, $name)
    {
        return $query->where('name', '=', $name);
    }

    public function findByApiKey($api_key)
    {
        return static::where('api_key', $api_key)->get();
    }

    public function scopeSearch($query, $value)
    {
        return $query->where('name', 'LIKE', "%$value%");
    }

    public function scopeFromUserWithContents($query, User $user)
    {
        
        return 
            $query->with(['contents' => function ($query2) {
                $query2->orderBy('order');
            }, 'contents.contained'])->whereHas('subscribed_users', function ($query2) use ($user) {
                $query2->where('user_id', $user->id);
            });
    }    
    
}
