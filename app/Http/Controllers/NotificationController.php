<?php

namespace App\Http\Controllers;

use App\Newsfeed;
use App\Notification;
use App\User;
use Illuminate\Support\Facades\Auth;
class NotificationController extends Controller
{
    
    protected function getFromUser(User $user)
    {
        $newsfeeds = Notification::fromUser($user)->with(['notifiable','notifiable.context'])->orderBy('created_at','desc')->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT',20));

        return response()->json($newsfeeds);
    }
    
    public function read(\Illuminate\Http\Request $request)
    {
        $this->validate($request, [
            'notifiable_id' => 'required|integer',
            'notifiable_type' => ['required', \Illuminate\Validation\Rule::in(Notification::TYPES)],
        ]);
        $notification = Notification::fromUserAndNotifiable(Auth::user(), $request->input('notifiable_type'), $request->input('notifiable_id'))->firstOrFail();
        
        return response()->json($notification->read()->save());
    }
    
}
