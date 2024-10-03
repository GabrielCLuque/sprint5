<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamesReadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\UsersTestSeeder::class]);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\GamesTestSeeder::class]);
    }

    public function test_show_returns_games_for_existing_user()
    {
        $user = User::where('email', 'firstposition@gmail.com')->first();

        $response = $this->getJson("/api/players/{$user->id}/games");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'user_id',
                'resultado_tirada_1',
                'resultado_tirada_2',
                'resultado_final',
                'victoria',
                'created_at',
                'updated_at',
            ]
        ]);
    }

    public function test_show_returns_404_if_user_not_found()
    {
        $nonExistentUserId = 99999;

        $response = $this->getJson("/api/players/{$nonExistentUserId}/games");

        $response->assertStatus(404);
        $response->assertJson([
            'message' => "No se ha encontrado ningún usuario con esas credenciales",
        ]);
    }

    public function test__user_has_no_games()
    {
        $userWithoutGames = User::where('email', 'anonimo@gmail.com')->first();

        $response = $this->getJson("/api/players/{$userWithoutGames->id}/games");

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Este usuario no ha jugado ninguna partida todavía',
        ]);
    }

    public function test_any_user_can_view_another_users_games()
    {
        $user = User::where('email', 'firstposition@gmail.com')->first();
        $otherUser = User::where('email', 'secondposition@gmail.com')->first();

        $response = $this->getJson("/api/players/{$otherUser->id}/games");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'user_id',
                'resultado_tirada_1',
                'resultado_tirada_2',
                'resultado_final',
                'victoria',
                'created_at',
                'updated_at',
            ]
        ]);
    }

    public function test_show_returns_multiple_games_for_user()
    {
        $user = User::where('email', 'firstposition@gmail.com')->first();

        $response = $this->getJson("/api/players/{$user->id}/games");

        $response->assertStatus(200);
        $response->assertJsonCount(2); // Verifica que el usuario tenga 2 juegos
    }

    public function test_show_returns_valid_date_format_for_games()
    {
        $user = User::where('email', 'firstposition@gmail.com')->first();

        $response = $this->getJson("/api/players/{$user->id}/games");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'created_at',
                'updated_at',
            ]
        ]);

        $responseData = $response->json();
        foreach ($responseData as $game) {
            $this->assertMatchesRegularExpression(
                '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}Z$/',
                $game['created_at'],
                'The created_at date format is invalid.'
            );
            $this->assertMatchesRegularExpression(
                '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}Z$/',
                $game['updated_at'],
                'The updated_at date format is invalid.'
            );
        }
    }
}
