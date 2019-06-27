<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Library\AttendanceControls\AttendanceIPControl;

/**
 * The attendance controls model class
 *
 * @author tanoinc
 */
class AttendanceControl extends Model
{
    
    CONST TYPE_IP = 'ip';
    CONST TYPES = [
        self::TYPE_IP => AttendanceIPControl::class,
    ];
    
    protected $table = 'attendance_control';

    protected $fillable = [
        'attendance_id',
        'type',
        'parameters'
    ];

    protected $hidden = [
        'id',
    ];
    
    protected $casts = [
        'created_at' => 'datetime:c',
        'updated_at' => 'datetime:c',
        'deleted_at' => 'datetime:c',
    ];

    public static function types() {
        return array_keys(static::TYPES);
    }

    public static function typeClasses() {
        return static::TYPES;
    }
    
    public static function toClass($type) {
        return static::TYPES[$type];
    }
    
    public static function toType($class) {
        return array_search($class, static::TYPES);
    }    

    
    public function attendance()
    {
        return $this->belongsTo('App\Attendance');
    }

    public static function fnWithAttendanceControl()
    {
        return function ($query) {
            return $query
                    ->addSelect('attendance_control.type as control_type')
                    ->leftJoin('attendance_control', 'attendance_control.attendance_id', '=', 'attendance.id');
        };
    }    
    
}
