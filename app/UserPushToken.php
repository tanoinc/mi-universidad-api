<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the "mi-universidad" user's push tokens.
 *
 * @author tanoinc
 */
class UserPushToken extends Model
{

    protected $table = 'user_push_token';
    protected $fillable = [
        'token', 'type'
    ];
    protected $hidden = [
        'id', 'deleted_at', 'created_at', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
}
