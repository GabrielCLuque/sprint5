<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        $this->artisan('db:seed', ['--class' => \Database\Seeders\UsersTestSeeder::class]);

        if (!file_exists(storage_path('oauth-private.key')) || !file_exists(storage_path('oauth-public.key'))) {
            \Artisan::call('passport:keys');
        }

        \Artisan::call('passport:client', [
            '--name' => 'TestClient',
            '--no-interaction' => true,
            '--personal' => true
        ]);
    }

    public function test_admin_can_login_and_receive_token()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'Admin@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user',
            'access_token',
        ]);
    }

    public function test_anonimo_can_login_and_receive_token()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'anonimo@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user',
            'access_token',
        ]);
    }

    public function test_login_fails_with_incorrect_password()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'Admin@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }

    public function test_login_fails_with_incorrect_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'wrongemail@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }

    public function test_login_fails_with_incorrect_email_and_password()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'wrongemail@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }
    public function test_login_fails_with_empty_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_login_fails_with_empty_password()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'Admin@gmail.com',
            'password' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_login_fails_with_empty_email_and_password()
    {
        $response = $this->postJson('/api/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_token_is_regenerated_on_multiple_logins()
    {
        $loginResponse1 = $this->postJson('/api/login', [
            'email' => 'Admin@gmail.com',
            'password' => 'password123',
        ]);

        $loginResponse1->assertStatus(200);
        $token1 = $loginResponse1->json('access_token');

        $loginResponse2 = $this->postJson('/api/login', [
            'email' => 'Admin@gmail.com',
            'password' => 'password123',
        ]);

        $loginResponse2->assertStatus(200);
        $token2 = $loginResponse2->json('access_token');

        $this->assertNotEquals($token1, $token2, 'The token should be regenerated on each login.');
    }
}
