<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\Newsfeed;
use Illuminate\Http\Request;
use App\User;
use App\UserApplication;
use App\Context;
use App\Application;
use App\Library\IonicApiV2;
use App\Notification;

/**
 * The Newsfeed controller class
 *
 * @author tanoinc
 */

class NewsfeedController extends Controller
{

    public function index()
    {
        $newsfeed = Newsfeed::all()->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT',20));

        return response()->json($newsfeed);
    }

    protected function getFromUser(User $user)
    {
        $newsfeeds = Newsfeed::fromUser($user)->orderBy('created_at','desc')->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT',20));

        return response()->json($newsfeeds);
    }

    public function createNewsfeed(Request $request, IonicApiV2 $ionic)
    {
        $newsfeed = $this->newFromRequest($request);
        $newsfeed->save();
        $this->setUsersFromRequest($newsfeed, $request);
        $notifications = null;
        if ($newsfeed->send_notification) {
            $notifications = $this->sendNotifications($ionic, $newsfeed);
            $newsfeed->notifications()->saveMany($notifications);
        }
        
        return response()->json(['newsfeed' => $newsfeed, 'notification_push_data_uuid' => $notifications[0]->push_data_uuid ]);
    }
    
    protected function sendNotifications(IonicApiV2 $ionic, Newsfeed $newsfeed) {
        $notifications = [];
        $recipients = $newsfeed->getUsersForNotification();
        if (Notification::NOTIFY_ALL_USERS == $recipients) {
            $recipients = IonicApiV2::RECIPIENT_ALL;
            $notifications[] = static::newNotification(null);
        } else {
            foreach ($recipients as $user) {
                $notifications[] = static::newNotification($user);
            }
        }
        try {
            $push_data_uuid = $ionic->sendPushNotification($recipients, $newsfeed->title, $newsfeed->content );
            foreach ($notifications as $notification ) {
                $notification->push_data_uuid = $push_data_uuid;
            }
        } catch (App\Exceptions\PushNotificationException $e) {
            // @TODO: Loguear la excepcion. Guardar "el hecho" que la notificacion no fue enviada.
        }

        return $notifications;
    }
    
    protected static function newNotification(User $user) 
    {
        $notification = new Notification();
        $notification->user()->associate($user);
        
        return $notification;
    }
    
    protected function newFromRequest(Request $request)
    {
        $newsfeed = new Newsfeed();
        
        return $this->setFromRequest($newsfeed, $request);
    }
    
    protected function setUsersFromRequest($newsfeed, Request $request)
    {
        $ids = $this->getUsersFromRequest($request)->map(function ($user_app) { return $user_app->user_id; });
        $newsfeed->users()->attach($ids);
    }
    protected function setFromRequest(Newsfeed $newsfeed, Request $request)
    {
        $newsfeed->application_id = $this->getApplication()->id;
        $newsfeed->title = $request->input('title');
        $newsfeed->content = $request->input('content');
        $newsfeed->send_notification = ($request->input('send_notification')?1:0);
        $newsfeed->global = ($request->input('global')?1:0);
        if ($request->has('context_name')) {
            $newsfeed->context_id = $this->getContext($this->getApplication(), $request->input('context_name'))->id;
        }
        
        return $newsfeed;
    }
    
    protected function getContext(Application $app, $context_name)
    {
        $context = Context::findByName($app, $context_name)->first();
        if (!$context) {
            $context = Context::create($app, $context_name);
        }

        return $context;
    }

    protected function getUsersFromRequest(Request $request)
    {
        $app_id = $this->getApplication()->id;
        if ($request->input('users')) {
            
            return UserApplication::findByApplicationAndExternalId( $app_id, $request->input('users') )->get();
        }
        return new \Illuminate\Support\Collection([]);
    }
}
