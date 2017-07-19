<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

/**
 * Represents the "mi-universidad" Notifications associated with notifiable objects.
 *
 * @author tanoinc
 */
class Notification extends Model
{

    const NOTIFY_ALL_USERS = "NOTIFY_ALL_USERS";
    
    protected $table = 'notification';
    protected $fillable = [
        'read_date', 'notifiable_type'
    ];
    protected $hidden = [
        'id', 'deleted_at', 'updated_at', 'user_id', 'notifiable_id', 'push_data_uuid'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function notifiable()
    {
        return $this->morphTo();
    }
    
    public function scopeFromUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
}
