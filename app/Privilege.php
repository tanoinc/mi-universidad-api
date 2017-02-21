<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * The privileges of an application
 *
 * @author tanoinc
 */
class Privilege extends Model
{
    protected $table = 'privilege';

    protected $fillable = [
        'name', 'description',
    ];
}
