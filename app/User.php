<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use \Laravel\Passport\HasApiTokens;
use App\User;
use App\Application;

/**
 * Represents the "mi-universidad" users.
 *
 * @author tanoinc
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{

    use HasApiTokens,
        Authenticatable,
        Authorizable;
    
    const ORIGIN_MOBILE = 'mobile';
    const ORIGIN_FACEBOOK = 'facebook';

    protected $table = 'user';
    protected $fillable = [
        'password', 'email'
    ];
    protected $hidden = [
        'password', 'id', 'deleted_at'
    ];

    public function applications()
    {
        return $this->belongsToMany('App\Application', 'user_application');
    }
    
    public function subscribed_applications()
    {
        return static::addApplicationSubscriptionCondition($this->applications());
    }
    
    public static function addApplicationSubscriptionCondition($query) 
    {
        return $query->whereNotNull('granted_privilege_version');
    }

    public function newsfeeds()
    {
        return $this->belongsToMany('App\Newsfeed')->orderBy('created_at', 'desc');
    }

    public function contexts()
    {
        return $this->belongsToMany('App\Context', 'context_user_subscription');
    }

    public function pushTokens()
    {
        return $this->hasMany('App\UserPushToken');
    }

    public function geolocations()
    {
        return $this->hasMany('App\Geolocation');
    }
    
    public function scopeFindByHashId($query, $hash_id)
    {
        return $query->where('hash_id', '=', $hash_id);
    }

    public static function registerByData($user_data, $origin = User::ORIGIN_MOBILE)
    {
        $user = static::firstOrNew(['username' => $user_data['username'], 'origin' => $origin ]);
        $user->hash_id = static::encodeHashId($user_data['username']);
        $user->origin = $origin;
        static::setData($user, $user_data);
        $user->save();
        
        $app = Application::findByName(env('MOBILE_APP_NAME'))->firstOrFail();
        $user->applications()->attach($app, ['granted_privilege_version' => $app->privilege_version, 'external_id' => $user->id]);
        
        return $user;
    }

    public static function updateByData($user_data)
    {
        $user = static::firstOrFail(['username' => $user_data['username']]);
        static::setData($user, $user_data);
        $user->save();

        return $user;
    }

    public static function setData(User $user, $user_data)
    {
        if (isset($user_data['password'])) {
            $user->password = static::encodePassword($user_data['password'], $user->origin);
        }
        if (isset($user_data['email'])) {
            $user->email = $user_data['email'];
        }
        if (isset($user_data['username'])) {
            $user->username = $user_data['username'];
        }
        if (isset($user_data['name'])) {
            $user->name = $user_data['name'];
        }
        if (isset($user_data['surname'])) {
            $user->surname = $user_data['surname'];
        }
    }

    public static function encodePassword($password, $origin= User::ORIGIN_MOBILE)
    {
        if ( $origin == static::ORIGIN_FACEBOOK ) {
            return $password;
        }
        return password_hash($password, PASSWORD_DEFAULT);        
    }

    public static function encodeHashId($username)
    {
        return sha1(random_bytes(8) . $username);
    }

    public static function usernameExists($username)
    {
        return User::where('username', '=', $username)->exists();
    }

    public static function emailExists($email)
    {
        return User::where('email', '=', $email)->exists();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }
    
    public function findForPassport($username) 
    {
        return $this->where('origin', static::ORIGIN_MOBILE)->where(function($q) use ($username){
            $q->where('email', $username)->orWhere('username', $username);
        })->first();
    }

}
