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
        'send_notification'
    ];
    
    protected $hidden = ['pivot'];

    public function application()
    {
        return $this->hasOne('App\Application');
    }
    
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
    
    public static function getAllFromUser($user)
    {
        $query = DB::table('newsfeed')->whereIn('id', function($query_in) use ($user) {
            $query_in->select('newsfeed_id')
                    ->from('newsfeed_user')
                    ->where('user_id', '=', $user->id);
        });
        $query2= DB::table('newsfeed')->
            where('global', '=', true)->
            whereIn('application_id', function($query_in) use ($user) {
                $query_in->select('application_id')
                        ->from('user_application')
                        ->where('user_id', '=', $user->id);
            })
        ->union($query);
        return $query2;
    }
   
}
