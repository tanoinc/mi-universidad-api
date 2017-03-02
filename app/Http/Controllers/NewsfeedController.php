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
        //\Illuminate\Support\Facades\DB::connection()->enableQueryLog();
        $user = User::findByHashId($user_hash_id)->firstOrFail();
        $newsfeeds = Newsfeed::getAllFromUser($user)->orderBy('created_at','desc')->get();
        //print_r(\Illuminate\Support\Facades\DB::getQueryLog());
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

    public function updateApplication(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        $application->name = $request->input('name');
        $application->description = $request->input('description');
        $application->secret_token = $request->input('secret_token');
        $application->save();

        return response()->json($application);
    }

}
