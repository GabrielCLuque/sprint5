<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use phpmock\phpunit\PHPMock;

class GamesCreateTest extends TestCase
{
    use RefreshDatabase;
    use PHPMock;

    protected function setUp(): void
    {
        parent::setUp();

        if (!file_exists(storage_path('oauth-private.key')) || !file_exists(storage_path('oauth-public.key'))) {
            \Artisan::call('passport:keys');
        }

        $this->artisan('db:seed', ['--class' => \Database\Seeders\UsersTestSeeder::class]);
    }

    public function test_user_can_create_game_for_themselves()
    {
        $user = User::where('email', 'Admin@gmail.com')->first();
        $this->actingAs($user, 'api');

        $response = $this->postJson("/api/players/{$user->id}/games");

        $response->assertStatus(201);

        $this->assertDatabaseHas('games', ['user_id' => $user->id]);
    }

    public function test_user_cannot_create_game_for_another_user()
    {
        $adminUser = User::where('email', 'Admin@gmail.com')->first();
        $anotherUser = User::where('email', 'RepeatedUser@gmail.com')->first();

        $this->actingAs($adminUser, 'api');

        $response = $this->postJson("/api/players/{$anotherUser->id}/games");

        $response->assertStatus(403);

        $response->assertJson(['message' => 'No puedes jugar partidas como otro usuario']);

        $this->assertDatabaseMissing('games', ['user_id' => $anotherUser->id]);
    }

    public function test_unauthenticated_users_cannot_create_games()
    {
        $user = User::where('email', 'Admin@gmail.com')->first();

        $response = $this->postJson("/api/players/{$user->id}/games");

        $response->assertStatus(401);
    }

    public function test_game_result_is_stored_correctly()
    {
        $user = User::where('email', 'Admin@gmail.com')->first();
        $this->actingAs($user, 'api');

        $response = $this->postJson("/api/players/{$user->id}/games");

        $response->assertStatus(201);

        $message = $response->json('message');

        preg_match('/Resultado primer dado: (\d+), Resultado segundo dado: (\d+), Resultado final: (\d+), (Has ganado|Has perdido)/', $message, $matches);

        $this->assertCount(5, $matches, 'El mensaje de respuesta no tiene el formato esperado.');

        $resultado_tirada_1 = (int)$matches[1];
        $resultado_tirada_2 = (int)$matches[2];
        $resultado_final = (int)$matches[3];
        $resultado_victoria = $matches[4] === 'Has ganado' ? 1 : 0;

        $this->assertGreaterThanOrEqual(1, $resultado_tirada_1);
        $this->assertLessThanOrEqual(6, $resultado_tirada_1);

        $this->assertGreaterThanOrEqual(1, $resultado_tirada_2);
        $this->assertLessThanOrEqual(6, $resultado_tirada_2);

        $this->assertEquals($resultado_tirada_1 + $resultado_tirada_2, $resultado_final);

        $expected_victoria = $resultado_final == 7 ? 1 : 0;
        $this->assertEquals($expected_victoria, $resultado_victoria);

        $game = $user->games()->latest()->first();

        $this->assertEquals($resultado_tirada_1, $game->resultado_tirada_1);
        $this->assertEquals($resultado_tirada_2, $game->resultado_tirada_2);
        $this->assertEquals($resultado_final, $game->resultado_final);
        $this->assertEquals($expected_victoria, $game->victoria);
    }

    //use of mock to see all posible game results send the correct messege
    public function test_multiple_games_results_are_correct()
    {
        $user = User::where('email', 'Admin@gmail.com')->first();
        $this->actingAs($user, 'api');

        for ($i = 0; $i < 10; $i++) {

            $existingGameIds = $user->games()->pluck('id')->toArray();

            $response = $this->postJson("/api/players/{$user->id}/games");

            $response->assertStatus(201);

            $message = $response->json('message');

            preg_match('/Resultado primer dado: (\d+), Resultado segundo dado: (\d+), Resultado final: (\d+), (Has ganado|Has perdido)/', $message, $matches);

            $this->assertCount(5, $matches, 'El mensaje de respuesta no tiene el formato esperado.');

            $resultado_tirada_1 = (int)$matches[1];
            $resultado_tirada_2 = (int)$matches[2];
            $resultado_final = (int)$matches[3];
            $resultado_victoria = $matches[4] === 'Has ganado' ? 1 : 0;

            $this->assertGreaterThanOrEqual(1, $resultado_tirada_1);
            $this->assertLessThanOrEqual(6, $resultado_tirada_1);

            $this->assertGreaterThanOrEqual(1, $resultado_tirada_2);
            $this->assertLessThanOrEqual(6, $resultado_tirada_2);

            $this->assertEquals($resultado_tirada_1 + $resultado_tirada_2, $resultado_final);

            $expected_victoria = $resultado_final == 7 ? 1 : 0;
            $this->assertEquals($expected_victoria, $resultado_victoria);

            $newGame = $user->games()->whereNotIn('id', $existingGameIds)->first();

            $this->assertNotNull($newGame, 'No se encontró el juego recién creado.');

            $this->assertEquals($resultado_tirada_1, $newGame->resultado_tirada_1);
            $this->assertEquals($resultado_tirada_2, $newGame->resultado_tirada_2);
            $this->assertEquals($resultado_final, $newGame->resultado_final);
            $this->assertEquals($expected_victoria, $newGame->victoria);
        }
    }

}
