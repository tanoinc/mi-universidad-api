<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\DB;

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
        'id', 'deleted_at', 'created_at', 'application_id', 'pivot'
    ];

    public function application()
    {
        return $this->belongsTo('App\Application');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'context_user_subscription');
    }

    public static function findByName(Application $app, $context_name, $format = false)
    {
        if ($format) {
            $context_name = static::formatContextName($context_name);
        }
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
        $this->description = str_replace(array('-', '_'), ' ', ucfirst(strtolower($value)));
    }

    public static function formatContextName($context_name) 
    {
        $context_name = preg_replace('~[^\\pL0-9_]+~u', '-', $context_name);
        $context_name = trim($context_name, "-");
        $context_name = iconv("utf-8", "us-ascii//TRANSLIT", $context_name);
        $context_name = strtolower($context_name);
        $context_name = preg_replace('~[^-a-z0-9_]+~', '', $context_name);
        return $context_name;
    }

    public static function create(Application $app, $context_name, $context_description = null)
    {
        $context = new Context();
        $context->name = static::formatContextName($context_name);
        $context->application_id = $app->id;
        if ($context_description) {
            $context->description = $context_description;
        } else {
            $context->description = $context_name;
        }
        if ($context->save()) {

            return $context;
        }

        return null;
    }

    public function scopeSearchInApplication($query, Application $app, $value)
    {
        $value = strtolower($value);
        return $query->where('application_id', $app->id)
                        ->where(function($q) use ($value) {
                            $q->where('name', 'LIKE', "%$value%")
                            ->orWhere('description', 'LIKE', "%$value%");
                        });
    }

    public static function findByAppAndUser($app_name, \App\User $user)
    {
        $query = DB::table('context')
                ->select('context.name', 'context.description', 'application.name AS application_name', 'context_user_subscription.created_at')
                ->join('application', 'application.id', 'context.application_id')
                ->join('context_user_subscription', 'context_user_subscription.context_id', 'context.id')
                ->join('user', 'user.id', 'context_user_subscription.user_id')
                ->where('application.name', $app_name)
                ->where('user.id', $user->id);

        return $query;
    }

}
