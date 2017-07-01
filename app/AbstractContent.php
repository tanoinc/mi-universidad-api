<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the "mi-universidad" Google Map content.
 *
 * @author tanoinc
 */
abstract class AbstractContent extends Model
{
   
    protected $fillable = [
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
    
    public function withValidator(\Illuminate\Validation\Validator $validator)
    {
        
    }

    public static function getCreationConstraints()
    {
        return [
            'name' => 'required|max:40',
            'description' => 'max:255',
            'icon_name' => 'max:50',
            'order' => 'integer',
            'cache_expiration' => 'date',
            'cache' => 'boolean',
            'send_user_info' => 'boolean',
            'url' => 'url',
        ];        
    }

    public static function getUpdateConstraints()
    {
        return [
            'name' => 'max:40',
            'description' => 'max:255',
            'icon_name' => 'max:50',
            'order' => 'integer',
            'cache_expiration' => 'date',
            'cache' => 'boolean',
            'send_user_info' => 'boolean',
            'url' => 'url',
        ];
    }
    
}
