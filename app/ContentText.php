<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the "mi-universidad" Google Map content.
 *
 * @author tanoinc
 */
class ContentText extends AbstractContent
{
   
    protected $table = 'content_text';
    protected $fillable = [
        'text',
        'url',
        'cache',
        'cache_expiration',
        'send_user_info',
    ];

}
