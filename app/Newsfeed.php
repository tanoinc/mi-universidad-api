<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * The newsfeed model class
 *
 * @author tanoinc
 */
class Newsfeed extends Model
{

    protected $table = 'newsfeed';
    protected $fillable = [
        'title',
        'content',
        'send_notification',
        'global',
        'context_id',
    ];
    protected $hidden = ['pivot'];

    public function application()
    {
        return $this->hasOne('App\Application');
    }

    public function context()
    {
        return $this->hasOne('App\Context');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public static function getAllFromUser($user)
    {
        $query = DB::table('newsfeed')
            ->select('newsfeed.*','application.description AS application_description', 'context.name as context_name','context.description as context_description')
            ->leftJoin('application', 'newsfeed.application_id', '=', 'application.id')
            ->leftJoin('context', 'newsfeed.context_id', '=', 'context.id')
            ->whereIn('newsfeed.id', function($query_in) use ($user) {
            $query_in->select('newsfeed_id')
                    ->from('newsfeed_user')
                    ->where('user_id', '=', $user->id);
        })
        ->whereNull('newsfeed.deleted_at');
        $query2 = DB::table('newsfeed')
            ->select('newsfeed.*','application.description AS application_description', 'context.name as context_name','context.description as context_description')
            ->where('global', '=', true)
            ->join('application', 'newsfeed.application_id', '=', 'application.id')
            ->leftJoin('context', 'newsfeed.context_id', '=', 'context.id')
            ->whereIn('newsfeed.application_id', function($query_in) use ($user) {
                $query_in->select('application_id')
                ->from('user_application')
                ->where('user_id', '=', $user->id);
            })
        ->whereNull('newsfeed.deleted_at')
        ->union($query);
        $query3 = DB::table('newsfeed')
            ->select('newsfeed.*','application.description AS application_description', 'context.name as context_name','context.description as context_description')
            ->leftJoin('application', 'newsfeed.application_id', '=', 'application.id')
            ->leftJoin('context', 'newsfeed.context_id', '=', 'context.id')
            ->whereIn('context_id', function($query_in) use ($user) {
                $query_in->select('context_id')
                ->from('context_user_subscription')
                ->where('user_id', '=', $user->id);
        })
        ->whereNull('newsfeed.deleted_at');
        $query2->union($query3);

        return $query2;
    }

}
