<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the "mi-universidad" Google Map content.
 *
 * @author tanoinc
 */
class ContentGoogleMap extends Model
{
   
    protected $table = 'content_google_map';
    protected $fillable = [
        'description',
        'url',
        'cache',
        'cache_expiration',
        'send_user_info',
    ];
    protected $hidden = [
        'id', 'created_at', 'updated_at','url'
    ];
    protected $appends = ['data_url'];
    public function contents()
    {
        return $this->morphMany('App\Content', 'contained');
    }
    
    public function getDataUrlAttribute() {
        if ($this->send_user_info) {
            return null;
        }
        return $this->url;
    }
}
