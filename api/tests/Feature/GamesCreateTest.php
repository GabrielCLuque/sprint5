<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Game;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GamesCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => \Database\Seeders\UsersTestSeeder::class]);
    }

    public function test_store_game_for_user()
    {
        $response = $this->postJson('/login', [
            'email' => 'Admin@gmail.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $token = $response->json('access_token');

        $user = User::where('email', 'Admin@gmail.com')->first();

        $storeResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson("/players/{$user->id}/games");

        $storeResponse->assertStatus(201);
        $this->assertDatabaseHas('games', ['user_id' => $user->id]);
    }

    public function test_cannot_store_game_as_another_user()
    {
        $response = $this->postJson('/login', [
            'email' => 'Admin@gmail.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $token = $response->json('access_token');

        $anotherUser = User::where('email', 'RepeatedUser@gmail.com')->first();

        $storeResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson("/players/{$anotherUser->id}/games");

        $storeResponse->assertStatus(201);
        $storeResponse->assertJson('No puedes jugar partidas como otro usuario');
    }
}
