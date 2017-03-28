<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\Newsfeed;
use Illuminate\Http\Request;
use App\User;

/**
 * The Newsfeed controller class
 *
 * @author tanoinc
 */
class NewsfeedController extends Controller
{

    public function index()
    {
        $newsfeed = Newsfeed::all();

        return response()->json($newsfeed);
    }

    public function getFromUser(Request $request, $user_hash_id)
    {
        $user = User::findByHashId($user_hash_id)->firstOrFail();
        $newsfeeds = Newsfeed::getAllFromUser($user)->orderBy('created_at','desc')->simplePaginate(15);

        return response()->json($newsfeeds->values());
    }

    public function createNewsfeed(Request $request)
    {
        $newsfeeds = Newsfeed::create($request->all());

        return response()->json($newsfeeds);
    }

    public function deleteApplication($id)
    {
        $application = Application::findOrFail($id);
        $application->delete();

        return response()->json('deleted');
    }
}
