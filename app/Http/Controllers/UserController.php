<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Application;
use Illuminate\Support\Facades\Auth;
use App\UserPushToken;

/**
 * The User controller class
 *
 * @author tanoinc
 */
class UserController extends Controller
{

    protected function getCreationConstraints()
    {
        return [
            'email' => 'required|email|unique:user|unique:user,username|max:255',
            'username' => 'required|alpha_dash|unique:user|unique:user,email|max:255',
            'name' => 'required|max:255',
            'surname' => 'required|max:255',
            'password' => 'required|min:8|max:255',
        ];
    }

    public function createUser(Request $request)
    {
        $this->validate($request, $this->getCreationConstraints());
        $user = User::registerByData($request->all());
        $app = Application::findByName(env('MOBILE_APP_NAME'))->firstOrFail();
        $user->applications()->attach($app, ['granted_privilege_version' => $app->privilege_version, 'external_id' => $user->id]);

        return response()->json($user);
    }

    public function getFromUser(User $user)
    {
        return $user;
    }

    public function registerPushToken(Request $request)
    {
        \Illuminate\Support\Facades\Log::debug(sprintf('register push token: user_id:[%s] token [%s], type [%s]', Auth::user()->id, $request->input('token'), $request->input('type')));
        $push_token = UserPushToken::firstOrNew([ 'user_id' => Auth::user()->id ]);
        $push_token->token = $request->input('token');
        $push_token->type = $request->input('type');
        $push_token->touch();
        
        return response()->json($push_token->save());
    }

    public function unregisterPushToken($token, $type)
    {
        $push_token = UserPushToken::where('user_id', Auth::user()->id)->firstOrFail();
        $push_token->delete();
        
        return $push_token;
    }
    
    public function registerLocation(Request $request)
    {
        \Illuminate\Support\Facades\Log::debug(sprintf('register location: user_id:[%s] coord [%s]', Auth::user()->id, json_encode($request->input('coords'))));
        $user_id = Auth::user()->id;
        $geolocation = \App\Geolocation::firstOrNew(['user_id' => $user_id]);
        $data = $request->all();
        $geolocation->fill($data['coords']);
        $geolocation->user_id = $user_id;
        $geolocation->touch();
        
        return response()->json($geolocation->save());
    }
}
