<?php namespace Mohsin\User\Tests;

use Auth;
use PluginTestCase;

class ApiDefaultLoginProviderTest extends PluginTestCase
{
    public function testValidLogin()
    {
        $testUser = [
            'email'                 => 'acmeuser@acme.com',
            'password'              => 'AcmePassword123!',
            'password_confirmation' => 'AcmePassword123!',
        ];
        Auth::register($testUser, true);

        $response = $this->json('POST', 'api/v1/account/signin', $testUser);
        $response->assertStatus(200)
                 ->assertJson([
                    'email' => 'acmeuser@acme.com',
                 ]);
    }
}
