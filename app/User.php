<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use \Laravel\Passport\HasApiTokens;

/**
 * Represents the "mi-universidad" users.
 *
 * @author tanoinc
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Authenticatable, Authorizable;
    
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
        return $this->belongsToMany('App\Newsfeed')->orderBy('created_at','desc');
    }
    
    public function contexts()
    {
        return $this->belongsToMany('App\Context', 'context_user_subscription');
    }
    
    public function scopeFindByHashId($query, $hash_id)
    {
        return $query->where('hash_id', '=', $hash_id);
    }

    public static function register($user_data)
    {
        $user_data['username'] = $user_data['email'];
        $user = static::firstOrNew(['username'=>$user_data['username']]);
        $user->hash_id = sha1(random_bytes(8).$user_data['username']);
        $user->password = password_hash($user_data['password'], PASSWORD_DEFAULT);
        $user->email = $user_data['email'];
        $user->username = $user_data['username'];
        $user->save();
        
        return $user;
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

}
