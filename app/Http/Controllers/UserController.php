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
            'email' => 'required|email|unique:user|max:255',
            'username' => 'required|alpha_dash|unique:user|max:255',
            'name' => 'required|max:255',
            'surname' => 'required|max:255',
            'password' => 'required|max:255',
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

}
