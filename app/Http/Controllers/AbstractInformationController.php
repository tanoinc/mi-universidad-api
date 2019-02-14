<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\AbstractInformation;
use Illuminate\Http\Request;
use App\User;
use App\UserApplication;
use App\Context;
use App\Application;
use App\Library\Generic\PushNotificationsInterface;
use App\Notification;

/**
 * The Ageneral Information controller abstract class
 *
 * @author tanoinc
 */

abstract class AbstractInformationController extends Controller
{
    abstract protected function getModelClass();
    abstract protected function getModelName();

    public function index()
    {
        $model_class = $this->getModelClass();
        $information = $model_class::all()->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT',20));

        return response()->json($information);
    }
    
    protected function getQueryFromUser(User $user) 
    {
        $model_class = $this->getModelClass();
        return $model_class::fromUser($user);
    }

    protected function getFromUser(User $user, $order_by = 'created_at', $order = 'desc')
    {
        $information = $this->getQueryFromUser($user)->orderBy($order_by,$order)->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT',20));

        return response()->json($information);
    }

    public function create(Request $request, PushNotificationsInterface $pushService)
    {
        $information = $this->newFromRequest($request);
        $information->save();
        $this->setUsersFromRequest($information, $request);
        $notifications = null;
        if ($information->send_notification) {
            $notifications = $this->sendNotifications($pushService, $information);
            $information->notifications()->saveMany($notifications);
        }
        $push_uuid = null;
        if (!empty($notifications)) {
            $push_uuid = $notifications[0]->push_data_uuid;
        }
        
        return response()->json([
            $this->getModelName() => $information, 
            'notification_push_data_uuid' => $push_uuid
        ]);
    }
    
    protected function sendNotifications(PushNotificationsInterface $pushService, AbstractInformation $information) {
        $notifications = [];
        $recipients = $information->getUsersForNotification();
        if (Notification::NOTIFY_ALL_USERS == $recipients) {
            $recipients = PushNotificationsInterface::RECIPIENT_ALL;
            $notifications[] = static::newNotification(null);
        } else {
            foreach ($recipients as $user) {
                $notifications[] = static::newNotification($user);
            }
        }
        try {
            $push_data_uuid = $pushService->sendPushNotification($recipients, $information->getNotificationTitle(), $information->getNotificationContent(), ['type'=> get_class($information), 'object' => $information] );
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
        $model_class = $this->getModelClass();
        $information = new $model_class();
        
        return $this->setFromRequest($information, $request);
    }
    
    protected function setUsersFromRequest(AbstractInformation $information, Request $request)
    {
        $ids = $this->getUsersFromRequest($request)->map(function ($user_app) { return $user_app->user_id; });
        $information->users()->attach($ids);
    }
    protected function setFromRequest(AbstractInformation $information, Request $request)
    {
        $this->validate($request, $this->getValidationRules());
        $information->application_id = $this->getApplication()->id;
        $information->send_notification = ($request->input('send_notification')?1:0);
        $information->global = ($request->input('global')?1:0);
        if ($request->has('context_name')) {
            $information->context_id = $this->getContext($this->getApplication(), $request->input('context_name'), ($request->has('context_description')?$request->input('context_description'):null) )->id;
        }
        
        return $this->setModelDataFromRequest($information, $request);
    }
    
    abstract protected function setModelDataFromRequest(AbstractInformation $information, Request $request);

    protected function getContext(Application $app, $context_name, $context_description = null)
    {
        $context = Context::findByName($app, $context_name, true)->first();
        if (!$context) {
            $context = Context::create($app, $context_name, $context_description);
        }

        return $context;
    }
    
    protected function getValidationRules()
    {
       return [
           'context_name' => 'alpha_dash|max:150',
           'context_description' => 'max:255',
       ];
    }

}
