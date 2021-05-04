<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CasrTest extends TestCase
{

    use RefreshDatabase;

    public function test_register_fails()
    {
        $response = $this->get('/api/register');

        $response->assertStatus(405);
    }

    public function test_register_validation_fails()
    {
        $response = $this->post('/api/register');

        $response->assertStatus(422);
        $this->assertTrue(isset($response['error']));
    }

    public function test_register_email_validation_fails()
    {
        $input = [
            'name' => 'Direct Ad Network',
            'address1' => 'Rock Heven Way',
            'address2' => '#125',
            'city' => 'Sterling',
            'state' => 'VA',
            'country' => 'USA',
            'phoneNo1' => '555-666-7777',
            'phoneNo2' => '',
            'zipCode' => 20166,
            'user' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john.doe.com',
                'phone' => '123-456-7890',
                'password' => 'Secret@123',
                'passwordConfirmation' => 'Secret@123'
            ]
        ];
        $response = $this->post('/api/register', $input);

        $response->assertStatus(422);
        $this->assertTrue(isset($response['error']) && isset($response['error']['email']));
    }

    public function test_register_password_confirmation_validation_fails()
    {
        $input = [
            'name' => 'Direct Ad Network',
            'address1' => 'Rock Heven Way',
            'address2' => '#125',
            'city' => 'Sterling',
            'state' => 'VA',
            'country' => 'USA',
            'phoneNo1' => '555-666-7777',
            'phoneNo2' => '',
            'zipCode' => 20166,
            'user' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '123-456-7890',
                'password' => 'Secret@123',
                'passwordConfirmation' => 'S'
            ]
        ];
        $response = $this->post('/api/register', $input);

        $response->assertStatus(422);
        $this->assertTrue(isset($response['error']) && isset($response['error']['user.passwordConfirmation']));
    }

    public function test_register_password_validation_fails()
    {
        $input = [
            'name' => 'Direct Ad Network',
            'address1' => 'Rock Heven Way',
            'address2' => '#125',
            'city' => 'Sterling',
            'state' => 'VA',
            'country' => 'USA',
            'phoneNo1' => '555-666-7777',
            'phoneNo2' => '',
            'zipCode' => 20166,
            'user' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '123-456-7890',
                'password' => 'Secret',
                'passwordConfirmation' => 'Secret'
            ]
        ];
        $response = $this->post('/api/register', $input);

        $response->assertStatus(422);
        $this->assertTrue(isset($response['error']) && isset($response['error']['user.password']));
    }

    public function test_register_zip_validation_fails()
    {
        $input = [
            'name' => 'Direct Ad Network',
            'address1' => 'Rock Heven Way',
            'address2' => '#125',
            'city' => 'Sterling',
            'state' => 'VA',
            'country' => 'USA',
            'phoneNo1' => '555-666-7777',
            'phoneNo2' => '',
            'zipCode' => "sdf",
            'user' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '123-456-7890',
                'password' => 'Secret@123',
                'passwordConfirmation' => 'Secret@123'
            ]
        ];
        $response = $this->post('/api/register', $input);

        $response->assertStatus(422);
        $this->assertTrue(isset($response['error']) && isset($response['error']['zipCode']));
    }

    public function test_register_success()
    {
        $input = [
            'name' => 'Direct Ad Network',
            'address1' => 'Rock Heven Way',
            'address2' => '#125',
            'city' => 'Sterling',
            'state' => 'VA',
            'country' => 'USA',
            'phoneNo1' => '555-666-7777',
            'phoneNo2' => '',
            'zipCode' => 20166,
            'user' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '123-456-7890',
                'password' => 'Secret@123',
                'passwordConfirmation' => 'Secret@123'
            ]
        ];
        $response = $this->post('/api/register', $input);

        $response->assertStatus(200);
    }

    public function test_account_success()
    {
        $response = $this->get('/api/account');

        $response->assertStatus(200);
        $this->assertTrue(isset($response['data']));
        $this->assertTrue(isset($response['links']));
        $this->assertTrue(isset($response['meta']) && isset($response['meta']['total']));
        $this->assertEquals($response['meta']['total'], 1);
    }
}
