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
        $push_token = UserPushToken::updateOrCreate([
            'token' => $request->input('token'),
            'type' => $request->input('type')
            ], [
            'user_id' => Auth::user()->id,
        ]);
    }

    public function unregisterPushToken(Request $request)
    {
        $token = UserPushToken::where('token', $request->input('token'))
            ->where('type', $request->input('type'))
            ->where('user_id', Auth::user()->id)->firstOrFail();
        
        $token->delete();
        
        return $token;
    }

}
