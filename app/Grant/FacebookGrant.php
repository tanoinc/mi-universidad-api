<?php
namespace App\Grant;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Facebook\Facebook;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * Description of FacebookGrant
 *
 * @author lucianoc
 */
class FacebookGrant extends \League\OAuth2\Server\Grant\AbstractGrant
{
    
    /**
     * @param UserRepositoryInterface         $userRepository
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
        $this->setUserRepository($userRepository);
        $this->setRefreshTokenRepository($refreshTokenRepository);

        $this->refreshTokenTTL = new \DateInterval('P1M');
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'facebook';
    }

    public function respondToAccessTokenRequest(ServerRequestInterface $request, \League\OAuth2\Server\ResponseTypes\ResponseTypeInterface $responseType, \DateInterval $accessTokenTTL)
    {
        $client = $this->getClient($request);
        $user_id = $this->validateUser($request);

        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user_id, []);
        $refreshToken = $this->issueRefreshToken($accessToken);

        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        return $responseType;
    }
    
    protected function getClient(ServerRequestInterface $request) {
        $client_id = $this->getRequestParameter('client_id', $request);
        if (is_null($client_id)) {
            throw OAuthServerException::invalidRequest('client_id');
        }
        
        return $this->clientRepository->getClientEntity(
            $client_id,
            $this->getIdentifier(),
            null,
            false
        );
    }
    
    protected function required($fields, $data, $info = '')
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) or is_null($data[$field])) {
                throw OAuthServerException::invalidRequest(($info?$info.'.':'').$field);
            }
        }
    }

    public function validateUser(ServerRequestInterface $request)
    {
        //$fb_data = $request->getParsedBody();
        $payload = $this->getRequestParameter('payload', $request);
        if (is_null($payload)) {
            throw OAuthServerException::invalidRequest('payload');
        }
        
        $this->required(array('status', 'authResponse'), $payload, 'payload');
        $this->required(array('userID', 'accessToken'), $payload['authResponse'], 'payload.authResponse');
        
        //{"status":"connected","authResponse":{"accessToken":"xxxx","expiresIn":"5180373","session_key":true,"sig":"...","userID":"123456789"}}
        if ($payload['status'] == 'connected') {
            $fb = new Facebook([
                'app_id' => env('FACEBOOK_APP_ID'),
                'app_secret' => env('FACEBOOK_SECRET'),
                'default_graph_version' => 'v2.10',
            ]);
            
            
            // Ref: https://github.com/facebook/php-graph-sdk/blob/5.5/docs/examples/retrieve_user_profile.md
            try {
                $response = $fb->get('/me?fields=id,first_name,picture,last_name,email', $payload['authResponse']['accessToken']);
                $me = $response->getGraphUser();
            }
            catch (\Facebook\Exceptions\FacebookResponseException $e) {
                throw OAuthServerException::serverError('Graph returned an error: ' . $e->getMessage());
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                throw OAuthServerException::serverError('Facebook SDK returned an error: ' . $e->getMessage());
            } catch (\Exception $e) {
                throw OAuthServerException::serverError($e->getMessage());
            }
            
            if ($me->getId() ==  $payload['authResponse']['userID']) {
                $user = \App\User::registerByData([
                    'username' => $me->getId(),
                    'name' => $me->getFirstName(),
                    'surname' => $me->getLastName(),
                    'email' => $me->getEmail(),
                    'password' => $payload['authResponse']['accessToken'],
                ], \App\User::ORIGIN_FACEBOOK);
                return $user->id;
            } else {
                throw OAuthServerException::invalidCredentials();
            }
        }
        throw OAuthServerException::invalidCredentials();
    }

}
