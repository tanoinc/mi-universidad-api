<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\CalendarEvent;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Http\Request;
/**
 * The Geolocation controller class
 *
 * @author tanoinc
 */
class GeolocationController extends Controller
{

    public function getFromUserHashId($user_hash_id)
    {
        $user = User::findByHashId($user_hash_id)->firstOrFail();
        $application = $user->subscribed_applications()->where('application_id', $this->getApplication()->id)->firstOrFail();
        
        return response()->json($user->geolocations()->first());
    }
    
    public function getFromUsers(Request $request)
    {
        
    }

}
