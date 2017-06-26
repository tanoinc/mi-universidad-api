<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the "mi-universidad" user's push tokens.
 *
 * @author tanoinc
 */
class Geolocation extends Model
{

    protected $table = 'geolocation';
    protected $fillable = [
        'altitude', 'latitude', 'longitude', 'accuracy', 'heading', 'speed'
    ];
    protected $hidden = [
        'id', 'deleted_at', 'created_at', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function scopeFindByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
    
}
