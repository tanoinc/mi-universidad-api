<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\Newsfeed;

/**
 * The Newsfeed controller class
 *
 * @author tanoinc
 */

class NewsfeedController extends AbstractInformationController
{

    protected function getModelClass()
    {
        return Newsfeed::class;
    }

    protected function getModelName()
    {
        return 'newsfeed';
    }

    protected function setModelDataFromRequest(\App\AbstractInformation $newsfeed, \Illuminate\Http\Request $request)
    {
        $newsfeed->title = $request->input('title');
        $newsfeed->content = $request->input('content');
        
        return $newsfeed;
    }
    
    protected function getQueryFromApplication(\App\Application $application)
    {
        return parent::getQueryFromApplication($application)->newestFirst();
    }
            
}
