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

    public function scopeMostRecent($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }
    
    public function isRecentlyUpdated($minutes = 5)
    {
        $someTimeAgo = new \DateTime();
        $someTimeAgo->sub(new \DateInterval("PT{$minutes}M"));
        
        return ($this->updated_at > $someTimeAgo);
    }
    
    public function isAccurate()
    {
        return $this->accuracy <= 100;
    }
}
