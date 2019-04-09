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
    
    const TYPE_NEWSFEED = 'App\Newsfeed';
    const TYPE_CALENDAR_EVENT = 'App\CalendarEvent';
    const TYPES = array(Notification::TYPE_CALENDAR_EVENT, Notification::TYPE_NEWSFEED);
    
    protected $casts = [
        'created_at' => 'datetime:c',
        'updated_at' => 'datetime:c',
        'read_date' => 'datetime:c',
    ];    
    
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
    
    public function read()
    {
        if (!$this->read_date) {
            $this->read_date = new \DateTime();
        }
        return $this;
    }


    public function scopeFromUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
    public function scopeFromUserAndNotifiable($query, User $user, $notifiable_type, $notifiable_id)
    {
        return $query->where('user_id', $user->id)->where('notifiable_type', $notifiable_type)->where('notifiable_id', $notifiable_id);
    }    
}
