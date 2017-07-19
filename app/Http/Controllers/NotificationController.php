<?php

namespace App\Http\Controllers;

use App\Newsfeed;
use App\Notification;
use App\User;

class NotificationController extends Controller
{
    
    protected function getFromUser(User $user)
    {
        $newsfeeds = Notification::fromUser($user)->with(['notifiable','notifiable.context'])->orderBy('created_at','desc')->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT',20));

        return response()->json($newsfeeds);
    }
    
}
