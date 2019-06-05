<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
    
    public static function fromUserBetweenDates(User $user, \DateTime $start, \DateTime $end)
    {
        $query = static::queryFromUser($user)->whereBetween('event_date', [$start, $end]);
        $query2 = static::queryFromApplication($user)->union($query)->whereBetween('event_date', [$start, $end]);
        $query3 = static::queryFromContext($user)->whereBetween('event_date', [$start, $end]);
        $query2->union($query3);

        return $query2;
    }
}
