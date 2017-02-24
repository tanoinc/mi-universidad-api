<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * The newsfeed model class
 *
 * @author tanoinc
 */
class Newsfeed extends Model
{
    protected $table = 'newsfeed';

    protected $fillable = [
        'title',
        'content',
        'send_notification',
        'application_id',
    ];
}
