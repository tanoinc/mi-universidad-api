<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * The attendance controls model class
 *
 * @author tanoinc
 */
class AttendanceControl extends Model
{
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
    
    public function attendance()
    {
        return $this->belongsTo('App\Attendance');
    }

}
