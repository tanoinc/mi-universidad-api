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

    public function getFromUser($user_hash_id)
    {
        $user = User::findByHashId($user_hash_id)->firstOrFail();
        $newsfeeds = $user->newsfeeds;
        $applications = $user->applications;
        foreach ($applications as $application) {
            $newsfeeds = array_merge($newsfeeds, $application->newsfeeds);
        }
        
        return response()->json($newsfeeds);
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
