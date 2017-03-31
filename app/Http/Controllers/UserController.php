<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Exceptions\AlreadyExistsException;
use App\Application;

/**
 * The User controller class
 *
 * @author tanoinc
 */
class UserController extends Controller
{
    protected function validateCreation(Request $request)
    {
        if (User::emailExists($request->get('email'))) {
            throw (new AlreadyExistsException())->setExtraData('email');
        }
        if (User::usernameExists($request->get('username'))) {
            throw (new AlreadyExistsException())->setExtraData('username');
        }        
    }

    public function createUser(Request $request)
    {
        $this->validateCreation($request);
        $user = User::register($request->all());
        $app = Application::findByName(env('MOBILE_APP_NAME'))->firstOrFail();
        $user->applications()->attach($app, ['granted_privilege_version' => $app->privilege_version, 'external_id' => $user->id]);
        
        return response()->json($user);
    }
}