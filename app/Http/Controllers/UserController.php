<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

/**
 * The User controller class
 *
 * @author tanoinc
 */
class UserController extends Controller
{
    public function createUser(Request $request)
    {
        $user = User::register($request->all());

        return response()->json($user);
    }
}