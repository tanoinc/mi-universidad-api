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
use App\UserApplication;
/**
 * The Geolocation controller class
 *
 * @author tanoinc
 */
class GeolocationController extends Controller
{

    public function getFromUserHashId(Request $request, $user_external_id)
    {
        $user_application = UserApplication::with('user')->findByApplicationAndExternalId( $this->getApplication()->id, $user_external_id )->firstOrFail();
        
        return response()->json($user_application->user()->first()->geolocations()->firstOrFail());
    }
    
    public function getFromUsers(Request $request)
    {
        $users = $this->getUsersFromRequest($request, ['user']);
        $locations = $users->mapWithKeys(function ($user, $key) {
            return [$user->external_id => $user->user()->first()->geolocations()->first()];
        });
        
        return response()->json($locations);
    }
    
}
