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
    const ATTENDANCE_SEND_NOTIFICATION = 'attendance:send_notification';
    const LEVEL_USER = 'user';
    const LEVEL_APPLICATION = 'application';
    
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
    public function scopeLevel($query, $level)
    {
        return $query->where('level', '=', $level);
    }
    public function scopeLevelUser($query)
    {
        return $this->scopeLevel($query, static::LEVEL_USER);
    }
    public function scopeLevelApplication($query)
    {
        return $this->scopeLevel($query, static::LEVEL_APPLICATION);
    }
}
