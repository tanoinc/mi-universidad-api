<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExternalAuthController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function facebook(Request $request)
    {
        \Illuminate\Support\Facades\Log::debug(sprintf('facebook login: data [%s]', json_encode($request->all())));
        $this->validate($request, [
            'status' => 'required',
            'authResponse' => 'array|min:1',
            'authResponse.accessToken' => 'required',
            'authResponse.userID' => 'required',
            'authResponse.expiresIn' => 'required',
        ]);
        $fb_data = $request->all();
        //{"status":"connected","authResponse":{"accessToken":"xxxx","expiresIn":"5180373","session_key":true,"sig":"...","userID":"123456789"}}
        if ($fb_data['status'] == 'connected') {
            $fb = new \Facebook\Facebook([
                'app_id' => env('FACEBOOK_APP_ID'),
                'app_secret' => env('FACEBOOK_SECRET'),
                'default_graph_version' => 'v2.10',
            ]);
            // Ref: https://github.com/facebook/php-graph-sdk/blob/5.5/docs/examples/retrieve_user_profile.md
            try {
                $response = $fb->get('/me?fields=id,first_name,picture,last_name,email', $fb_data['authResponse']['accessToken']);
                $me = $response->getGraphUser();
            }
            catch (\Facebook\Exceptions\FacebookResponseException $e) {
                throw new \App\Exceptions\UnauthorizedAccessException('Graph returned an error: ' . $e->getMessage());
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                throw new \App\Exceptions\UnauthorizedAccessException('Facebook SDK returned an error: ' . $e->getMessage());
            } catch (\Exception $e) {
                throw new \App\Exceptions\UnauthorizedAccessException($e->getMessage());
            }
            
            if ($me->getId() ==  $fb_data['authResponse']['userID']) {
                $user = \App\User::registerByData([
                    'username' => $me->getId(),
                    'name' => $me->getFirstName(),
                    'surname' => $me->getLastName(),
                    'email' => $me->getEmail(),
                    'password' => $fb_data['authResponse']['accessToken'],
                ], \App\User::ORIGIN_FACEBOOK);
                return response()->json($user);
            }
        }
        throw new \App\Exceptions\UnauthorizedAccessException();
    }

}
