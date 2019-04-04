<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the "mi-universidad" client's version compatibility 
 * with the current api version
 *
 * @author tanoinc
 */
class ClientCompatibility extends Model
{

    protected $table = 'client_compatibility';
    protected $fillable = [
        'client_version'
    ];

    public function scopeFindByVersion($query, $version)
    {
        return $query->whereRaw( ':version like client_version', ['version' => $version] );
    }
}
