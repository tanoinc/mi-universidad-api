<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * Description of InitTest
 *
 * @author lucianoc
 */
class InitTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testInitConfig()
    {
        $this->json('GET', '/api/v1/config/init', [])
                ->seeJson([
                    "client_id" => env('OAUTH_CLIENT_ID'),
                    "client_secret" => env('OAUTH_CLIENT_SECRET'),
        ]);
    }

}
