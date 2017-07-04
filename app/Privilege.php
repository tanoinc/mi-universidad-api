<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * The privileges of an application
 *
 * @author tanoinc
 */
class Privilege extends Model
{
    const NEWSFEED_SEND_NOTIFICATION = 'newsfeed:send_notification';
    const CALENDAR_EVENT_SEND_NOTIFICATION = 'calendar_event:send_notification';
    
    protected $table = 'privilege';

    protected $fillable = [
        'name', 'description','controller_action'
    ];
    
    protected $hidden = ['pivot', 'created_at', 'updated_at', 'id', 'controller_action'];
    
    public function scopeFindByName($query, $name)
    {
        return $query->where('name', '=', $name);
    }
    public function scopeFindByAction($query, $name)
    {
        return $query->where('controller_action', '=', $name);
    }
}
