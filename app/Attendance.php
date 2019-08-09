<?php

namespace App;

/**
 * The newsfeed model class
 *
 * @author tanoinc
 */
class Attendance extends AbstractInformation
{

    const TABLE_NAME = 'attendance';

    protected $fillable = [
        'name',
        'description',
        'location',
        'start_time',
        'end_time',
        'send_notification',
        'global',
        'context_id',
    ];
    
    protected $casts = [
        'start_time' => 'datetime:c',
        'end_time' => 'datetime:c',
        'created_at' => 'datetime:c',
        'updated_at' => 'datetime:c',
        'deleted_at' => 'datetime:c',
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'attendance_user');
    }

    public function attendanceUser()
    {
        return $this->belongsTo('App\AttendanceUser');
    }

    public function controls()
    {
        return $this->hasMany(AttendanceControl::class);
    }
    
    protected function getPrivilegeSendNotification()
    {
        return Privilege::ATTENDANCE_SEND_NOTIFICATION;
    }

    public function getNotificationContent()
    {
        return $this->name;
    }

    public function getNotificationTitle()
    {
        return $this->start_time->format(env('DATE_FORMAT_READABLE','d/m/Y H:i'));
    }

    public function isActive()
    {
        return true;
    }

    public function getAttendanceUser(User $user) {
        return AttendanceUser::firstOrNew([ 'attendance_id' => $this->id, 'user_id' => $user->id ]);
    }

    public function setAttendanceUserPresent(User $user)
    {
        $attendance_user = $this->getAttendanceUser($user);
        $attendance_user->setStatusPresent();

        return $attendance_user;
    }
    
    public static function prepareForHydrate($array) {
        //@TODO: Refactoring
        if (empty($array)) {
            return [];
        }
        
        if (!property_exists($array[0], 'control_type')) {
            return $array;
        }
        
        $hydrated_array = [];
        $id = null;
        $i = 0;
        foreach ($array as $raw_attendance) {
            if ($raw_attendance->id != $id) {
                $hydrated_array[] = $raw_attendance;
                $hydrated_array[$i]->controls = [];
                $id = $raw_attendance->id;
                $i++;
            }
            if ($raw_attendance->control_type != null) {
                if (!$type = AttendanceControl::toType($raw_attendance->control_type)) {
                    throw new \Exception($raw_attendance->control_type.' control class not defined in AttendanceControl');
                }
                $hydrated_array[$i-1]->controls[] = $type;
            }
            if (property_exists($hydrated_array[$i-1], 'control_type')) {
                unset($hydrated_array[$i-1]->control_type);
            }            
        }
        
        return $hydrated_array;
    }

}
