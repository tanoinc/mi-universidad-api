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
use App\UserApplication;
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
        $newsfeed = $this->newFromRequest($request);
        $newsfeed->save();
        $this->setUsersFromRequest($newsfeed, $request);
        
        return response()->json($newsfeed);
    }
    
    protected function newFromRequest(Request $request)
    {
        $newsfeed = new Newsfeed();
        
        return $this->setFromRequest($newsfeed, $request);
    }
    
    protected function setUsersFromRequest($newsfeed, Request $request)
    {
        $ids = $this->getUsersFromRequest($request)->map(function ($user_app) { return $user_app->user_id; });
        $newsfeed->users()->attach($ids);
    }
    protected function setFromRequest($newsfeed, Request $request)
    {
        $newsfeed->application_id = $this->getApplication()->id;
        $newsfeed->title = $request->input('title');
        $newsfeed->content = $request->input('content');
        $newsfeed->send_notification = ($request->input('send_notification')?1:0);
        $newsfeed->global = ($request->input('global')?1:0);
        
        return $newsfeed;
    }
    
    protected function getUsersFromRequest(Request $request)
    {
        $app_id = $this->getApplication()->id;
        if ($request->input('users')) {
            
            return UserApplication::findByApplicationAndExternalId( $app_id, $request->input('users') )->get();
        }
    }
}
