<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\UserPushToken;
use App\Exceptions\AcceptedRequestForgotPasswordException;
use App\Exceptions\RejectedCodeForgotPasswordException;
use App\Exceptions\AcceptedCodeForgotPasswordException;

/**
 * The User controller class
 *
 * @author tanoinc
 */
class UserController extends Controller
{

    protected function getCreationConstraints()
    {
        //@TODO: Validar que los campos de email y usuario sean unicos respecto del "origen" (solo para app móvil, en la registracion)
        return [
            'email' => 'required|email|unique:user|unique:user,username|max:255',
            'username' => 'required|alpha_dash|unique:user|unique:user,email|max:255',
            'name' => 'required|max:255',
            'surname' => 'required|max:255',
            'password' => 'required|min:8|max:255',
        ];
    }

    public function createUser(Request $request)
    {
        $this->validate($request, $this->getCreationConstraints());
        $user = User::registerByData($request->all());

        return response()->json($user);
    }

    public function getFromUser(User $user)
    {
        return $user;
    }

    public function registerPushToken(Request $request)
    {
        \Illuminate\Support\Facades\Log::debug(sprintf('register push token: user_id:[%s] token [%s], type [%s]', Auth::user()->id, $request->input('token'), $request->input('type')));
        $push_token = UserPushToken::firstOrNew([ 'user_id' => Auth::user()->id]);
        $push_token->token = $request->input('token');
        $push_token->type = $request->input('type');
        $push_token->user_id = Auth::user()->id;
        $push_token->touch();

        return response()->json($push_token->save());
    }

    public function unregisterPushToken($token, $type)
    {
        $push_token = UserPushToken::where('user_id', Auth::user()->id)->firstOrFail();
        $push_token->delete();

        return $push_token;
    }

    public function registerLocation(Request $request)
    {
        \Illuminate\Support\Facades\Log::debug(sprintf('register location: user_id:[%s] input [%s]', Auth::user()->id, json_encode($request->all())));
        $user_id = Auth::user()->id;
        $geolocation = \App\Geolocation::firstOrNew(['user_id' => $user_id]);
        $data = $request->all();
        if (isset($data['coords'])) {
            $geolocation->fill($data['coords']);
            $geolocation->user_id = $user_id;
            $geolocation->touch();

            return response()->json($geolocation->save());
        }
        return response()->json(false);
    }
    
    public function forgotPassword(Request $request)
    {
        $this->validate($request, [ 'email' => 'required|email|max:255' ]);
        $user = User::findByEmail($request->get('email'))->first();
        if ($user) {
            $this->mailRecoveryPassword($user);
            $user->save();
        }
        throw new AcceptedRequestForgotPasswordException();
    }
    
    public function forgotPasswordReset(Request $request)
    {
        $code_length = env('MAIL_RECOVER_PASSWORD_CODE_LENGTH', 6);
        $this->validate($request, [
            'email'=> 'required|email|max:255',
            'code' => 'required|alpha_num|max:'.$code_length.'|min:'.$code_length,
            'password' => 'required|min:8|max:255',
        ]);
        $user = User::findByEmail($request->get('email'))->first();
        if ($user) {
            if ($user->isRecoveryCodeValid($request->get('code'))) {
                $user->setPassword($request->get('password'));
                $user->save();
                throw new AcceptedCodeForgotPasswordException();
            } else {
                $user->save();
            }
        }
        throw new RejectedCodeForgotPasswordException();
    }
    
    protected function mailRecoveryPassword($user) {
        $code_length = env('MAIL_RECOVER_PASSWORD_CODE_LENGTH', 6);
        $subject = env('MAIL_RECOVER_PASSWORD_SUBJECT','Mi Universidad: Password recovery');
        $message = env('MAIL_RECOVER_PASSWORD_MSG','Your \'Mi Universidad\' password recovery code is: %s');
        return $this->mail($user, sprintf($message, $user->recoverPassword($code_length)), $subject);
    }
    
    protected function mail($user, $msg, $subject) {
        return app('mailer')->raw($msg, function($msg) use ($user, $subject) { $msg->to([$user->email]); $msg->subject($subject); });        
    }

}
