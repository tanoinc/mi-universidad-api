<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Represents the "mi-universidad" user attendance status to attendance events.
 *
 * @author tanoinc
 */
class AttendanceUser extends Model
{

    const STATUS_PRESENT = 'p';

    protected $table = 'attendance_user';
    protected $fillable = [
        'attendance_id', 'user_id', 'status'
    ];
    protected $hidden = [
        'id', 'deleted_at', 'created_at'
    ];
    protected $casts = [
        'created_at' => 'datetime:c',
        'updated_at' => 'datetime:c',
        'deleted_at' => 'datetime:c',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function attendance()
    {
        return $this->belongsTo('App\Attendance');
    }

    public function scopeFindByAttendanceAndUser($query, Attendance $attendance, User $user)
    {
        return $this->scopeFindByApplicationIdAndUserId($query, $attendance->id, $user->id);
    }

    public function scopeFindByAttendanceIdAndUserId($query, $attendance_id, $user_id)
    {
        $query->where('attendance_id', '=', $attendance_id);
        if (is_array($user_id)) {
            $query->whereIn('user_id', $user_id);
        } else {
            $query->where('user_id', '=', $user_id);
        }

        return $query;
    }

    public function setStatusPresent()
    {
        $this->status = static::STATUS_PRESENT;
    }

    public static function fnWithAttendanceUser(User $user)
    {
        return function ($query) use ($user) {
            if (!is_int($user->id)) {
                throw new Exception('Invalid user id type');
            }
            return $query
                            ->addSelect('attendance_user.status as status')
                            ->leftJoin('attendance_user', function($join) use ($user) {
                                $join->on('attendance_user.attendance_id', '=', 'attendance.id');
                                $join->on('attendance_user.user_id', '=', DB::raw($user->id));
                            })
            ;
        };
    }

    public function scopeStatusWithExternalIds($query, Attendance $attendance)
    {
        $query
                ->select('user_application.external_id', 'attendance_user.status', 'attendance_user.created_at', 'attendance_user.updated_at')
                ->join('attendance', 'attendance.id', '=', 'attendance_user.attendance_id')
                ->join('user_application', function ($join) {
                    $join->on('user_application.user_id', '=', 'attendance_user.user_id')
                            ->on('user_application.application_id', '=', 'attendance.application_id');
                            
                })
                ->where('attendance.id', '=', $attendance->id)
        ;

        return $query;
    }
    
    public function scopePresents($query)
    {
        return $query
                ->where('attendance_user.status', '=', static::STATUS_PRESENT)
        ;
    }
}
