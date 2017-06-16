<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the "mi-universidad" generic content.
 *
 * @author tanoinc
 */
class Content extends Model
{
   
    protected $table = 'content';
    protected $fillable = [
        'name',
        'description',
        'icon_name',
        'order',
    ];
    protected $hidden = [
        'deleted_at', 'updated_at', 'application_id', 'contained_id'
    ];

    public function application()
    {
        return $this->belongsTo('App\Application', 'application_id');
    }
    
    public function contained()
    {
        return $this->morphTo();
    }
    
    public function scopeFromApplication($query, Application $app)
    {
        return $query->where('application_id', $app->id);
    }
}
