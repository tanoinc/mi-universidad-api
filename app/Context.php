<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

/**
 * Represents the "mi-universidad" contexts related to contextual modules.
 *
 * @author tanoinc
 */
class Context extends Model
{

    protected $table = 'context';
    protected $fillable = [
        'name', 'description'
    ];
    protected $hidden = [
        'id', 'deleted_at', 'created_at', 'application_id','pivot'
    ];

    public function application()
    {
        return $this->belongsTo('App\Application');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'context_user_subscription');
    }
    
    public static function findByName(Application $app, $context_name)
    {
        return $app->contexts()->where('name', '=', $context_name);
    }

    public function subscribe(User $user) 
    {
        $this->users()->syncWithoutDetaching([$user->id]);
    }
    
    public function unsubscribe(User $user) 
    {
        $this->users()->detach($user);
    }
    
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
        $this->description = str_replace(array('-','_'),' ', ucfirst(strtolower($value)));
    }
    public static function create(Application $app, $context_name)
    {
        $context = new Context();
        $context->name = $context_name;
        $context->application_id = $app->id;
        if ($context->save())
        {
            
            return $context;
        }
        
        return null;
    }
}
