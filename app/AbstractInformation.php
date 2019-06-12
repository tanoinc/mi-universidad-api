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
 * The general abstract class of notifiable information (with context)
 *
 * @author tanoinc
 */
abstract class AbstractInformation extends Model
{

    protected $fillable = [
        'send_notification',
        'global',
        'context_id',
    ];
    protected $hidden = ['pivot', 'application_id', 'context_id'];
    
    protected $users_notification = null;

    public function __construct(array $attributes = array())
    {
        $this->table = static::TABLE_NAME;
        parent::__construct($attributes);
    }
    
    public static function boot()
    {
        parent::boot();
        static::deleting([static::class, 'preDelete']);
    }
    
    public static function preDelete(AbstractInformation $value)
    {
        $value->notifications()->delete();
    }

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
    
    protected abstract function getPrivilegeSendNotification();
    public abstract function getNotificationTitle();
    public abstract function getNotificationContent();
    
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
            if ($application->first()->has_granted_privilege($this->getPrivilegeSendNotification()) ) { 
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
    
    protected static function queryFromUser($user)
    {
        $query = DB::table(static::TABLE_NAME)
            ->select(static::TABLE_NAME.'.*','application.description AS application_description', 'context.name as context_name','context.description as context_description')
            ->leftJoin('application', static::TABLE_NAME.'.application_id', '=', 'application.id')
            ->leftJoin('context', static::TABLE_NAME.'.context_id', '=', 'context.id')
            ->whereIn(static::TABLE_NAME.'.id', function($query_in) use ($user) {
            $query_in->select(static::TABLE_NAME.'_id')
                    ->from(static::TABLE_NAME.'_user')
                    ->where('user_id', '=', $user->id);
        })
        ->whereNull(static::TABLE_NAME.'.deleted_at');
        
        return $query;
    }

    protected static function queryFromApplication($user)
    {
        $query2 = DB::table(static::TABLE_NAME)
            ->select(static::TABLE_NAME.'.*','application.description AS application_description', 'context.name as context_name','context.description as context_description')
            ->where('global', '=', true)
            ->join('application', static::TABLE_NAME.'.application_id', '=', 'application.id')
            ->leftJoin('context', static::TABLE_NAME.'.context_id', '=', 'context.id')
            ->whereIn(static::TABLE_NAME.'.application_id', function($query_in) use ($user) {
                $query_in->select('application_id')
                ->from('user_application')
                ->where('user_id', '=', $user->id);
                User::addApplicationSubscriptionCondition($query_in);
            })
        ->whereNull(static::TABLE_NAME.'.deleted_at');
            
        return $query2;
    }

    protected static function queryFromContext($user)
    {
        $query3 = DB::table(static::TABLE_NAME)
            ->select(static::TABLE_NAME.'.*','application.description AS application_description', 'context.name as context_name','context.description as context_description')
            ->leftJoin('application', static::TABLE_NAME.'.application_id', '=', 'application.id')
            ->leftJoin('context', static::TABLE_NAME.'.context_id', '=', 'context.id')
            ->whereIn('context_id', function($query_in) use ($user) {
                $query_in->select('context_id')
                ->from('context_user_subscription')
                ->where('user_id', '=', $user->id);
        })
        ->whereNull(static::TABLE_NAME.'.deleted_at');
            
        return $query3;
    }
    
    public static function fromUser(User $user, $fn_filter = null)
    {
        if (!$fn_filter) {
            $fn_filter = function($query) {
                return $query;
            };
        }
        $query = $fn_filter(static::queryFromUser($user));
        $query2 = $fn_filter(static::queryFromApplication($user));
        $query3 = $fn_filter(static::queryFromContext($user));

        $query2->union($query)->union($query3);
        
        return $query2;
    }
    
}
