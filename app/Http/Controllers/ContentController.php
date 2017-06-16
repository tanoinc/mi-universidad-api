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
/**
 * Description of ApplicationController
 *
 * @author tanoinc
 */
class ContentController extends Controller
{

    protected static $content_types = [
        'google_map' => ContentGoogleMap::class,
    ];
    
    protected static function contentTypeParse($content_type)
    {
        if (!isset(static::$content_types[$content_type])) {
            throw new \App\Exceptions\CustomValidationException('Content type unknown.');
        }
        
        return static::$content_types[$content_type];
    }

    protected function getCreationConstraints()
    {
        return [
            'name' => 'required|max:40',
            'description' => 'max:255',
            'icon_name' => 'max:50',
            'order' => 'integer',
            'cache_expiration' => 'date',
            'cache' => 'boolean',
            'send_user_info' => 'boolean',
            'url' => 'url',
        ];
    }
    
    protected function getUpdateConstraints()
    {
        return [
            'name' => 'max:40',
            'description' => 'max:255',
            'icon_name' => 'max:50',
            'order' => 'integer',
            'cache_expiration' => 'date',
            'cache' => 'boolean',
            'send_user_info' => 'boolean',
            'url' => 'url',
        ];
    }
    
    public function index(Request $request)
    {
        $contents = Content::fromApplication($this->getApplication())->with(['contained'])->orderBy('order','asc')->get();
        
        return response()->json($contents);
    }
    
    protected function saveFromRequest($content, Request $request, $content_type = null) 
    {
        $content->fill($request->all());
        $content->application_id = $this->getApplication()->id;
        $content->save();
        
        if ($content_type) {
            $content_type_class = static::contentTypeParse($content_type);
            $content_type_object = new $content_type_class;
        } else {
            $content_type_object = $content->contained()->first();
        }
        $content_type_object->fill($request->all());
        $content_type_object->send_user_info = ($request->input('send_user_info') == true or $request->input('send_user_info') == 'true' or $request->input('send_user_info') == 1);
        $content_type_object->cache = ($request->input('cache') == true or $request->input('cache') == 'true' or $request->input('cache') == 1);
        $content_type_object->save();
        $content_type_object->contents()->saveMany([$content]);
        
        return $content;
    }

    public function create(Request $request, $content_type)
    {
        $this->validate($request, $this->getCreationConstraints());
        
        return response()->json($this->saveFromRequest( new Content(), $request, $content_type));
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
        $this->validate($request, $this->getUpdateConstraints());
        if ($content->application_id != $this->getApplication()->id) {
            throw new ForbiddenAccessException();
        }

        return response()->json( $this->saveFromRequest($content, $request) );
    }

}
