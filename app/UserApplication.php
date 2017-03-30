<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the "mi-universidad" user in an application.
 *
 * @author tanoinc
 */
class UserApplication extends Model
{

    protected $table = 'user_application';
    protected $fillable = [
        'external_id', 'application_id', 'granted_privilege_version'
    ];
    protected $hidden = [
        'id', 'deleted_at', 'created_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function application()
    {
        return $this->belongsTo('App\Application');
    }
    
    public function scopeFindByApplicationAndExternalId($query, $application_id, $external_id)
    {
        $query->where('application_id', '=', $application_id);
        if (is_array($external_id)) {
            $query->whereIn('external_id', $external_id);
        } else {
            $query->where('external_id', '=', $external_id);
        }
        
        return $query;
    }
    
}
