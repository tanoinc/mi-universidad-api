<?php

use App\Http\Controllers\ConfigurationController;
use Mockery\Mock;

/**
 * Test for ConfigurationController controller 
 *
 * @author lucianoc
 */
class ConfigurationControllerTest extends TestCase
{

    public function testConfigurationController_RootAccess_StatusOk()
    {
        $this->get('/');

        $this->assertEquals(200, $this->response->getStatusCode());
    }
    
    public function testConfigurationController_RootAccess_ResponseHelloAppName()
    {
        putenv("MOBILE_APP_NAME=testApp");
        
        $this->get('/');

        $this->assertEquals('Hello from testApp!', $this->response->getContent());
    }
    
    public function testConfigurationController_InitializationConfiguration_StatusOk()
    {
        $this->get('/v1/config/init');

        $this->assertEquals(200, $this->response->getStatusCode());
    }

    
    public function testConfigurationController_InitializationConfiguration_Content()
    {
        putenv("OAUTH_CLIENT_ID=test_client_id");
        putenv("OAUTH_CLIENT_SECRET=test_client_secret");
        putenv("CONTACT_EMAIL=test_email@email.com");
        putenv("CONTACT_SUBJECT=test_subject");
        putenv("MAIL_RECOVER_PASSWORD_CODE_RETRY_TIME=10");
        $revision = file_get_contents(__DIR__.'/../../../../REVISION');
        
        $expected = [
            'client_id' => 'test_client_id', 
            'client_secret' => 'test_client_secret',
            'contact_email' => 'test_email@email.com',
            'contact_subject' => 'test_subject',
            'recover_password_retry_time' => '10',
            'api_version' => trim($revision),
        ];
        
        $this
            ->json('GET', '/v1/config/init')
            ->seeJsonEquals($expected);
    }
    
}
