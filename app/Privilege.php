<?php

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
        'name', 'description', 'controller_action'
    ];
    protected $hidden = ['pivot', 'created_at', 'updated_at', 'id', 'controller_action', 'applications'];

    public function applications()
    {
        return $this->belongsToMany('App\Application', 'application_privilege');
    }

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

    public static function boot()
    {
        parent::boot();
        static::deleting([static::class, 'preDelete']);
    }

    public static function preDelete(Privilege $privilege)
    {
        $privilege->applications()->detach();
    }

    public static function removeFromModel($model_table)
    {
        static::where('name', 'like', $model_table . '%')->get()->each(
            function($privilege) {
                $privilege->delete();
            }
        );        
    }

    public static function createCrud($model_table, $controller)
    {
        static::create([
            'name' => $model_table . ':post',
            'description' => 'To create a new item in the ' . $model_table,
            'controller_action' => 'App\\Http\\Controllers\\' . $controller . '@create',
            'level' => 'user',
        ]);
        static::create([
            'name' => $model_table . ':put',
            'description' => 'To update an item from the ' . $model_table,
            'controller_action' => 'App\\Http\\Controllers\\' . $controller . '@update',
            'level' => 'user',
        ]);
        static::create([
            'name' => $model_table . ':delete',
            'description' => 'To delete an item from the ' . $model_table,
            'controller_action' => 'App\\Http\\Controllers\\' . $controller . '@delete',
            'level' => 'user',
        ]);
        static::create([
            'name' => $model_table . ':get',
            'description' => 'To get an item from the ' . $model_table,
            'controller_action' => 'App\\Http\\Controllers\\' . $controller . '@get',
            'level' => 'application',
        ]);
        static::create([
            'name' => $model_table . ':send_notification',
            'description' => 'Send ' . $model_table . ' push notifictions to users mobile app',
            'controller_action' => 'App\\Http\\Controllers\\' . $controller . '@create',
            'level' => 'user',
        ]);
    }

}
