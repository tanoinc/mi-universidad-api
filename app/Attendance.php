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

}
