<?php

namespace App\Http\Controllers;

use Laravel\Passport\Passport;
use Dusterio\LumenPassport\LumenPassport;

class ExternalAccessTokenController extends \Dusterio\LumenPassport\Http\Controllers\AccessTokenController
{
    public function issueToken(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $this->server->enableGrantType(
            $this->makeFacebookGrant(), LumenPassport::tokensExpireIn(null, env('OAUTH_CLIENT_ID'))
        );
        
        return parent::issueToken($request);
    }
    
    /**
     * Create and configure a Facebook grant instance.
     *
     * @return \App\Grant\FacebookGrant
     */
    protected function makeFacebookGrant()
    {
        $grant = new \App\Grant\FacebookGrant(
            app()->make(\Laravel\Passport\Bridge\UserRepository::class),
            app()->make(\Laravel\Passport\Bridge\RefreshTokenRepository::class)
        );
        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
