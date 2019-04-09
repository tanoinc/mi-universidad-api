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
class CalendarEvent extends AbstractInformation
{

    const TABLE_NAME = 'calendar_event';

    protected $fillable = [
        'event_name',
        'event_date',
        'event_duration',
        'send_notification',
        'global',
        'context_id',
    ];
    protected $casts = [
        'event_date' => 'datetime:c',
        'created_at' => 'datetime:c',
        'updated_at' => 'datetime:c',
        'deleted_at' => 'datetime:c',
    ];    

    protected function getPrivilegeSendNotification()
    {
        return Privilege::CALENDAR_EVENT_SEND_NOTIFICATION;
    }

    public function getNotificationContent()
    {
        return $this->event_name;
    }

    public function getNotificationTitle()
    {
        return $this->event_date;
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
