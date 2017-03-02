<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of Application
 *
 * @author tanoinc
 */
class Application extends Model
{

    protected $table = 'application';
    protected $fillable = [
        'name', 'description',
    ];
    protected $hidden = [
        'token_secret',
    ];

    public function privileges()
    {
        return $this->belongsToMany('App\Privilege');
    }

    public function granted_privileges()
    {
        return $this->privileges()->wherePivot('version', '=', $this->privilege_version);
    }

    public function newsfeeds()
    {
        return $this->hasMany('App\Newsfeed');
    }
    public function global_newsfeeds($union = null)
    {
        $q = $this->newsfeeds()->where('global', true)->orderBy('created_at', 'desc');
        if ($union) {
            $q->union($union);
        }
        return $q;
    }    

}
