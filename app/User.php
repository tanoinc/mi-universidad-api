<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * Represents the "mi-universidad" users.
 *
 * @author tanoinc
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    
    protected $table = 'user';

    protected $fillable = [
        'username', 'hash_id', 'email'
    ];

    protected $hidden = [
        'password',
    ];
    
    public function applications()
    {
        return $this->belongsToMany('App\Application', 'user_application');
    }
    
    public function newsfeeds()
    {
        return $this->belongsToMany('App\Newsfeed')->orderBy('created_at','desc');
    }
    
    public function scopeFindByHashId($query, $hash_id)
    {
        return $query->where('hash_id', '=', $hash_id);
    }    
}
