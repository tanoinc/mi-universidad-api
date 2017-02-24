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
}
