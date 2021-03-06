<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the "mi-universidad" Google Map content.
 *
 * @author tanoinc
 */
class ContentGoogleMap extends AbstractContent
{
   
    protected $table = 'content_google_map';
    protected $fillable = [
        'description',
        'url',
        'cache',
        'cache_expiration',
        'send_user_info',
    ];
}
