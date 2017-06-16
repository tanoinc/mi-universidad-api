<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use \App\Application;
use Illuminate\Http\Request;

/**
 * Description of ApplicationController
 *
 * @author tanoinc
 */
class ContentController extends Controller
{

    public function index(Request $request)
    {
        return response()->json($this->getApplication());
    }

    public function create(Request $request)
    {
        $application = Application::create($request->all());

        return response()->json($application);
    }

    public function delete($id)
    {
        $application = Application::findOrFail($id);
        $application->delete();

        return response()->json('deleted');
    }

    public function update(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        $application->name = $request->input('name');
        $application->description = $request->input('description');
        $application->secret_token = $request->input('secret_token');
        $application->save();

        return response()->json($application);
    }

}
