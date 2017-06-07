<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * The newsfeed model class
 *
 * @author tanoinc
 */
class Newsfeed extends Model
{

    protected $table = 'newsfeed';
    protected $fillable = [
        'title',
        'content',
        'send_notification',
        'global',
        'context_id',
    ];
    protected $hidden = ['pivot', 'application_id', 'context_id'];
    
    protected $users_notification = null;

    public function application()
    {
        return $this->belongsTo('App\Application');
    }

    public function context()
    {
        return $this->belongsTo('App\Context');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }
    
    public function notifications()
    {
        return $this->morphMany('App\Notification', 'notifiable');
    }
    
    public function isMobileAppGlobal()
    {
        return false; // @TODO: verificar que la aplicacions sea la movil y que la notificacion sea global
    }
    
    public function getUsersForNotification()
    {
        if ($this->users_notification != null) {
            return $this->users_notification;
        }
        if ( $this->isMobileAppGlobal() ) {
            $this->users_notification = Notification::NOTIFY_ALL_USERS;
            return $this->users_notification;
        }
        $this->users_notification = [];
        if ($this->send_notification and $application = $this->application()) {
            // @TODO: verificar que la version de los privilegios sea la misma que la version aceptada por el usuario.
            // if appliction has the privilege to send notifications
            if ($application->first()->has_granted_privilege(Privilege::NEWSFEED_SEND_NOTIFICATION) ) { 
                if ($this->global) {
                    // All application's users
                    $this->users_notification = $application->users()->get();
                } else {
                    // Specific notification recipients (private)
                    $this->users_notification = $this->users()->get();
                }
                if ($context = $this->context()->first()) {
                    // All context subscribed users
                    $this->users_notification = $this->users_notification->merge( $context->users()->get() );
                }
            }
        }
        return $this->users_notification;
    }
    
    public static function fromUser($user)
    {
        $query = DB::table('newsfeed')
            ->select('newsfeed.*','application.description AS application_description', 'context.name as context_name','context.description as context_description')
            ->leftJoin('application', 'newsfeed.application_id', '=', 'application.id')
            ->leftJoin('context', 'newsfeed.context_id', '=', 'context.id')
            ->whereIn('newsfeed.id', function($query_in) use ($user) {
            $query_in->select('newsfeed_id')
                    ->from('newsfeed_user')
                    ->where('user_id', '=', $user->id);
        })
        ->whereNull('newsfeed.deleted_at');
        $query2 = DB::table('newsfeed')
            ->select('newsfeed.*','application.description AS application_description', 'context.name as context_name','context.description as context_description')
            ->where('global', '=', true)
            ->join('application', 'newsfeed.application_id', '=', 'application.id')
            ->leftJoin('context', 'newsfeed.context_id', '=', 'context.id')
            ->whereIn('newsfeed.application_id', function($query_in) use ($user) {
                $query_in->select('application_id')
                ->from('user_application')
                ->where('user_id', '=', $user->id);
            })
        ->whereNull('newsfeed.deleted_at')
        ->union($query);
        $query3 = DB::table('newsfeed')
            ->select('newsfeed.*','application.description AS application_description', 'context.name as context_name','context.description as context_description')
            ->leftJoin('application', 'newsfeed.application_id', '=', 'application.id')
            ->leftJoin('context', 'newsfeed.context_id', '=', 'context.id')
            ->whereIn('context_id', function($query_in) use ($user) {
                $query_in->select('context_id')
                ->from('context_user_subscription')
                ->where('user_id', '=', $user->id);
        })
        ->whereNull('newsfeed.deleted_at');
        $query2->union($query3);

        return $query2;
    }

}
