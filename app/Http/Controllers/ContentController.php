<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\Application;
use Illuminate\Http\Request;
use App\Content;
use App\Exceptions\ForbiddenAccessException;
use App\ContentGoogleMap;
use App\ContentText;
use Illuminate\Support\Facades\Auth;
/**
 * Description of ApplicationController
 *
 * @author tanoinc
 */
class ContentController extends Controller
{

    protected static $content_types = [
        'google_map' => ContentGoogleMap::class,
        'text' => ContentText::class,
    ];
    
    protected static function contentTypeParse($content_type)
    {
        if (!isset(static::$content_types[$content_type])) {
            throw new \App\Exceptions\CustomValidationException('Content type unknown.');
        }

        return static::$content_types[$content_type];
    }

    public function index(Request $request)
    {
        $contents = Content::fromApplication($this->getApplication())
                ->with(['contained'])
                ->orderBy('order','asc')
                ->get();

        return response()->json($contents);
    }
    
    public function get($id)
    {
        $content = Content::fromApplication($this->getApplication())
                ->with(['contained'])
                ->findOrFail($id);
        
        return response()->json($content);
    }
    
    protected function saveFromRequest($content, Request $request, $content_type_class = null)
    {
        $content->fill($request->all());
        $content->application_id = $this->getApplication()->id;

        if ($content_type_class) {
            $content_type_object = new $content_type_class;
        } else {
            $content_type_object = $content->contained()->first();
        }
        $content_type_object->fill($request->all());
        $content_type_object->send_user_info = ($request->input('send_user_info') == true or $request->input('send_user_info') == 'true' or $request->input('send_user_info') == 1);
        $content_type_object->cache = ($request->input('cache') == true or $request->input('cache') == 'true' or $request->input('cache') == 1);
        $content_type_object->save();
        $content_type_object->contents()->saveMany([$content]);
        $content->save();

        return $content;
    }

    public function create(Request $request, $content_type)
    {
        $content_type_class = static::contentTypeParse($content_type);
        $this->validate($request, $content_type_class::getCreationConstraints());

        return response()->json($this->saveFromRequest( new Content(), $request, $content_type_class));
    }

    public function delete($id)
    {
        $content = Content::findOrFail($id);
        if ($content->application_id != $this->getApplication()->id) {
            throw new ForbiddenAccessException();
        }
        $content->delete();

        return response()->json($content);
    }

    public function update(Request $request, $id)
    {
        $content = Content::findOrFail($id);
        $content_type_object = $content->contained()->first();
        $content_type_class = get_class($content_type_object);
        $this->validate($request, $content_type_class::getUpdateConstraints());
        if ($content->application_id != $this->getApplication()->id) {
            throw new ForbiddenAccessException();
        }

        return response()->json( $this->saveFromRequest($content, $request) );
    }

    protected function getFromUser(\App\User $user)
    {
        $contents = Application::fromUserWithContents($user)->get();

        return response()->json($contents);
    }

    public function getFromApplication(Request $request, $application_name)
    {
        $contents = Application::with('contents.contained')->findByName($application_name)->get();

        return response()->json($contents);
    }

    public function getFromUrl(Request $request, $content_id)
    {
        $content = Content::with(['contained','application'])->findOrFail($content_id);
        $http = new \App\Library\Http($content->contained->url);
        $response = null;
        if ($content->contained->send_user_info) {
            $user_application = \App\UserApplication::findByApplicationIdAndUserId($content->application_id, Auth::user()->id)->firstOrFail();
            $data = $request->all();
            \Illuminate\Support\Facades\Log::debug(sprintf('Content data received: [%s]', str_replace("\n",'', file_get_contents("php://input")) ));
            if (!empty($data))
            {
                $this->saveGeolocation($data);
            }
            $data['external_id'] = $user_application->external_id;
            $response = json_decode( $http->post($data) );
        } else {
            $response = json_decode($http->get());
        }

        return response()->json( $response );
    }

    public function saveGeolocation($data)
    {
        $geolocation = null;
        if (isset($data['coords']) and isset($data['coords']['latitude']) and isset($data['coords']['longitude']))
        {
            $user_id = Auth::user()->id;
            $geolocation = \App\Geolocation::firstOrNew(['user_id' => $user_id]);
            $geolocation->fill($data['coords']);
            $geolocation->user_id = $user_id;
            $geolocation->save();
        }
        return $geolocation;
    }

}
