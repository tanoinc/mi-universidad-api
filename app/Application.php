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
        'name', 'description', 'auth_callback_url', 'auth_required'
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

    public function scopeExceptApp($query, $app_name)
    {
        return $query->where('name', 'NOT LIKE', $app_name);
    }

    public function scopeNotSubscribedBy($query, User $user)
    {
        return $query->whereNotIn('id', function ($query_in) use ($user) {
            $query_in->select('application_id')
            ->from('user_application')
            ->where('user_id', '=', $user->id);
            User::addApplicationSubscriptionCondition($query_in);     
        });
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
    
    public function generate_api_hashes()
    {
        $this->generate_api_key();
        $this->generate_api_secret();
    }
    
    public function generate_api_key()
    {
        $this->api_key = static::generate_hash();
    }
    
    public function generate_api_secret()
    {
        $this->api_secret = static::generate_hash();
    }
    
    protected static function generate_hash()
    {
        return sha1(random_bytes(8).microtime());
    }
    
    public function setFromArray($array_data)
    {
        $this->name = $array_data['name'];
        $this->description = $array_data['description'];
        $this->auth_required = $array_data['auth_required'];
        $this->auth_callback_url = $array_data['auth_callback_url'];
    }
    
    public static function create($array_data, $privileges = null) {
        $app = new Application();
        $app->privilege_version = 1;
        $app->setFromArray($array_data);
        $app->generate_api_hashes();
        $app->save();
        if (!$privileges) {
            $privileges = Privilege::levelUser()->get();
            $privileges = $privileges->merge(Privilege::levelApplication()->get());
        }
        $app->privileges()->attach($privileges);
        return $app;
    }
}
