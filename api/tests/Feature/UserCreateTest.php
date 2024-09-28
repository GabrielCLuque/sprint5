<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\UsersTestSeeder;
use PHPUnit\Framework\Attributes\DataProvider;

class UserCreateTest extends TestCase
{
    use HasApiTokens, HasFactory, Notifiable, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => UsersTestSeeder::class]);
    }

    #[DataProvider('userProvider')]
    public function test_user_store_validation($email, $password, $user_name, $expectedStatus, $expectedErrorField)
    {
        $response = $this->postJson('/api/players', [
            'email' => $email,
            'password' => $password,
            'user_name' => $user_name,
        ]);

        $response->assertStatus($expectedStatus);

        if ($expectedErrorField) {
            $response->assertJsonValidationErrors($expectedErrorField);
        }
    }

    public static function userProvider(): array
    {
        return [
            'repeated_email' => [
                'email' => 'RepeatedUser@gmail.com',
                'password' => 'password123',
                'user_name' => 'NewUser',
                'expectedStatus' => 422,
                'expectedErrorField' => 'email',
            ],
            'repeated_username' => [
                'email' => 'uniqueuser@example.com',
                'password' => 'password123',
                'user_name' => 'RepeatedUser',
                'expectedStatus' => 422,
                'expectedErrorField' => 'user_name',
            ],
            'short_password' => [
                'email' => 'newuser@example.com',
                'password' => 'short',
                'user_name' => 'NewUser',
                'expectedStatus' => 422,
                'expectedErrorField' => 'password',
            ],
            'invalid_email' => [
                'email' => 'invalid-email',
                'password' => 'password123',
                'user_name' => 'NewUser',
                'expectedStatus' => 422,
                'expectedErrorField' => 'email',
            ],
            'empty_email' => [
                'email' => '',
                'password' => 'password123',
                'user_name' => 'NewUser',
                'expectedStatus' => 422,
                'expectedErrorField' => 'email',
            ],
            'empty_username' => [
                'email' => 'newuser@example.com',
                'password' => 'password123',
                'user_name' => '',
                'expectedStatus' => 201,
                'expectedErrorField' => null,
            ],
            'attempt_to_set_user_anonimo_valid' => [
                'email' => 'anonimo1@example.com',
                'password' => 'password123',
                'user_name' => 'Anonimo',
                'expectedStatus' => 201,
                'expectedErrorField' => null,
            ],
            'attempt_to_set_user_anonimo_again_valid' => [
                'email' => 'anonimo2@example.com',
                'password' => 'password123',
                'user_name' => 'Anonimo',
                'expectedStatus' => 201,
                'expectedErrorField' => null,
            ],
            'attempt_to_set_user_repeated_invalid' => [
                'email' => 'repeatedagain@example.com',
                'password' => 'password123',
                'user_name' => 'RepeatedUser',
                'expectedStatus' => 422,
                'expectedErrorField' => 'user_name',
            ],
            'username_only_spaces' => [
                'email' => 'newuserwithspaces@example.com',
                'password' => 'password123',
                'user_name' => '   ',
                'expectedStatus' => 201,
                'expectedErrorField' => null,
            ],
            'case_insensitive_duplicate_username' => [
                'email' => 'duplicatecaps@example.com',
                'password' => 'password123',
                'user_name' => 'REPEATEDUSER',
                'expectedStatus' => 422,
                'expectedErrorField' => 'user_name',
            ],
            'batman_symbol_username' => [
                'email' => 'batmanfan@example.com',
                'password' => 'password123',
                'user_name' => '🦇',
                'expectedStatus' => 422,
                'expectedErrorField' => 'user_name',
            ],
        ];
    }
}
