<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use \Laravel\Passport\HasApiTokens;
use App\User;
use App\Application;

/**
 * Represents the "mi-universidad" users.
 *
 * @author tanoinc
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{

    use HasApiTokens,
        Authenticatable,
        Authorizable;

    const ORIGIN_MOBILE = 'mobile';
    const ORIGIN_FACEBOOK = 'facebook';

    protected $table = 'user';
    protected $fillable = [
        'password', 'email'
    ];
    protected $hidden = [
        'password', 'id', 'deleted_at', 'recover_password_value'
    ];

    public function applications()
    {
        return $this->belongsToMany('App\Application', 'user_application');
    }

    public function subscribed_applications()
    {
        return static::addApplicationSubscriptionCondition($this->applications());
    }

    public static function addApplicationSubscriptionCondition($query)
    {
        return $query->whereNotNull('granted_privilege_version');
    }

    public function newsfeeds()
    {
        return $this->belongsToMany('App\Newsfeed')->orderBy('created_at', 'desc');
    }

    public function contexts()
    {
        return $this->belongsToMany('App\Context', 'context_user_subscription');
    }

    public function attendances()
    {
        return $this->belongsToMany('App\Attendance', 'attendance_user');
    }

    public function pushTokens()
    {
        return $this->hasMany('App\UserPushToken');
    }

    public function geolocations()
    {
        return $this->hasMany('App\Geolocation');
    }

    public function scopeFindByHashId($query, $hash_id)
    {
        return $query->where('hash_id', '=', $hash_id);
    }

    public function scopeFindByEmail($query, $email, $origin = User::ORIGIN_MOBILE)
    {
        return $query->where('email', '=', $email)->where('origin', $origin);
    }

    protected static function normalize(&$data)
    {
        $data['username'] = strtolower(trim($data['username']));
        $data['email'] = strtolower(trim($data['email']));
    }

    public static function registerByData($user_data, $origin = User::ORIGIN_MOBILE)
    {
        static::normalize($user_data);
        $user = static::firstOrNew(['username' => $user_data['username'], 'origin' => $origin]);
        $new = (!$user->hash_id);
        if ($new) {
            $user->hash_id = static::encodeHashId($user_data['username']);
            $user->origin = $origin;
            $user->unconfirm();
        }
        static::setData($user, $user_data);
        $user->save();
        if ($new) {
            $app = Application::findByName(env('MOBILE_APP_NAME'))->firstOrFail();
            $user->applications()->attach($app, ['granted_privilege_version' => $app->privilege_version, 'external_id' => $user->id]);
        }

        return $user;
    }

    public static function updateByData($user_data)
    {
        $user = static::firstOrFail(['username' => $user_data['username']]);
        static::setData($user, $user_data);
        $user->save();

        return $user;
    }

    public function setPassword($password)
    {
        $this->password = static::encodePassword($password, $this->origin);
    }

    public function confirm()
    {
        $this->confirmed = true;
    }

    public function unconfirm()
    {
        $this->confirmed = false;
    }

    public static function setData(User $user, $user_data)
    {
        if (isset($user_data['password'])) {
            $user->setPassword($user_data['password']);
        }
        if (isset($user_data['email'])) {
            $user->email = $user_data['email'];
        }
        if (isset($user_data['username'])) {
            $user->username = $user_data['username'];
        }
        if (isset($user_data['name'])) {
            $user->name = $user_data['name'];
        }
        if (isset($user_data['surname'])) {
            $user->surname = $user_data['surname'];
        }
    }

    public function canSendRecoveryCode()
    {
        if (!$this->origin == User::ORIGIN_MOBILE) {
            return false;
        }

        if (!$this->last_password_recovery) {
            return true;
        }

        $exiprationTime = new \DateTime($this->last_password_recovery);
        $exiprationTime->modify('+' . env('MAIL_RECOVER_PASSWORD_CODE_RETRY_TIME', '10') . ' minutes');
        $now = new \DateTime();

        return ($now >= $exiprationTime);
    }

    public function recoverPassword()
    {
        if ($this->origin == User::ORIGIN_MOBILE) {
            $this->recover_password_value = static::generateRecoverPasswordValue();
            $this->recover_password_count = 10;
            $this->last_password_recovery = new \DateTime();
        } else {
            throw new \Exception($this->origin . ' not supported for password recovery.');
        }

        return $this->recover_password_value;
    }

    public function isRecoveryCodeValid($code)
    {

        if (!$this->canRecoverPassword()) {
            return false;
        }

        if (strtoupper($this->recover_password_value) !== strtoupper($code)) {
            $this->recover_password_count--;
            return false;
        }

        $this->recover_password_value = null;
        $this->recover_password_count = null;
        $this->last_password_recovery = null;
        $this->confirm();

        return true;
    }

    public static function encodePassword($password, $origin = User::ORIGIN_MOBILE)
    {
        if ($origin == static::ORIGIN_FACEBOOK) {
            return $password;
        }
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function generateRecoverPasswordValue($long = 6)
    {
        return strtoupper(substr(bin2hex(random_bytes($long)), 0, $long));
    }

    public static function encodeHashId($username)
    {
        return sha1(random_bytes(8) . $username);
    }

    public static function usernameExists($username)
    {
        return User::where('username', '=', $username)->exists();
    }

    public static function emailExists($email)
    {
        return User::where('email', '=', $email)->exists();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function findForPassport($username)
    {
        return $this->where('origin', static::ORIGIN_MOBILE)->where(function($q) use ($username) {
                    $q
                            ->where('confirmed', true)
                            ->where(function($q2) use ($username) {
                                $username = strtolower($username);
                                $q2->whereRaw('LOWER(email) LIKE ?', $username)->orWhereRaw('LOWER(username) LIKE ?', $username);
                            });
                })->first();
    }

    protected function canRecoverPassword()
    {
        return !($this->recover_password_count === null or $this->recover_password_value === null or $this->recover_password_count <= 0);
    }

}
