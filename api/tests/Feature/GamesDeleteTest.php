<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class GamesDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (!file_exists(storage_path('oauth-private.key')) || !file_exists(storage_path('oauth-public.key'))) {
            \Artisan::call('passport:keys');
        }

        $this->artisan('db:seed', ['--class' => \Database\Seeders\UsersTestSeeder::class]);
        $this->artisan('db:seed', ['--class' => \Database\Seeders\GamesTestSeeder::class]);
    }

    public function test_admin_can_delete_user_games()
    {
        $admin = User::where('email', 'Admin@gmail.com')->first();
        Passport::actingAs($admin);

        $usergamestodelete = User::where('email', 'firstposition@gmail.com')->first();

        $response = $this->deleteJson("/api/players/{$usergamestodelete->id}/games");

        $response->assertStatus(201);

        //looking on the database that the delete worked
        $this->assertDatabaseMissing('games', ['user_id' => $usergamestodelete->id]);
    }

    public function test_non_admin_user_cannot_delete_games()
    {
        $user = User::where('email', 'firstposition@gmail.com')->first();
        Passport::actingAs($user);

        $otheruser = User::where('email', 'secondposition@gmail.com')->first();
        $response = $this->deleteJson("/api/players/{$otheruser->id}/games");

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Esta acción requiere el estatus de administrador']);

        $this->assertDatabaseHas('games', ['user_id' => $otheruser->id]);
    }

    public function test_unauthenticated_user_cannot_delete_games()
    {
        $usergamestodelete = User::where('email', 'firstposition@gmail.com')->first();

        $response = $this->deleteJson("/api/players/{$usergamestodelete->id}/games");

        $response->assertStatus(401);

        $this->assertDatabaseHas('games', ['user_id' => $usergamestodelete->id]);
    }

    public function test_non_admin_user_cannot_delete_their_own_games()
    {
        $user = User::where('email', 'firstposition@gmail.com')->first();
        Passport::actingAs($user);

        $response = $this->deleteJson("/api/players/{$user->id}/games");

        $response->assertStatus(403)
                 ->assertJson(['error' => 'Esta acción requiere el estatus de administrador']);

        $this->assertDatabaseHas('games', ['user_id' => $user->id]);
    }
}
