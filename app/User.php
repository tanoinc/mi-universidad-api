<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use \Laravel\Passport\HasApiTokens;
use App\User;

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

    public function scopeFindByHashId($query, $hash_id)
    {
        return $query->where('hash_id', '=', $hash_id);
    }

    public static function registerByData($user_data)
    {
        $user = static::firstOrNew(['username' => $user_data['username']]);
        $user->hash_id = static::encodeHashId($user_data['username']);
        static::setData($user, $user_data);
        $user->save();

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
            $user->password = static::encodePassword($user_data['password']);
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

    public static function encodePassword($password)
    {
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
        return $this->where('email', $username)->orWhere('username', $username)->first();
    }

}
