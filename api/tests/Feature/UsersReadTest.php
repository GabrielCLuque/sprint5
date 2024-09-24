<?php

namespace Tests\Unit;


use App\Models\User;
use App\Models\Game;
use Tests\TestCase;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\UsersTestSeeder;
use Database\Seeders\GamesTestSeeder;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;

class UsersReadTest extends TestCase
{
    use HasApiTokens, HasFactory, Notifiable, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => UsersTestSeeder::class]);
        $this->artisan('db:seed', ['--class' => GamesTestSeeder::class]);
    }

    public function test_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token',
        ])->getJson('/api/players');

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_admin_try_GET_Players()
    {
        $adminUser = User::where('email', 'Admin@gmail.com')->first();
        $this->actingAs($adminUser, 'api');

        $response = $this->getJson('/api/players');

        $response->assertStatus(200);
        $response->assertJson([
            ['user_name' => 'RepeatedUser', 'percentage' => 0.00],
            ['user_name' => 'Admin', 'percentage' => 0.00],
            ['user_name' => 'Anonimo', 'percentage' => 0.00],
            ['user_name' => 'User First Position', 'percentage' => 100.00],
            ['user_name' => 'User First Position COPY', 'percentage' => 100.00],
            ['user_name' => 'User Second Position', 'percentage' => 50.00],
            ['user_name' => 'User Second Position COPY', 'percentage' => 50.00],
            ['user_name' => 'User Third Position', 'percentage' => 0.00],
            ['user_name' => 'User SixtySix Percent', 'percentage' => 66.67],
        ]);
    }

    public function test_non_admin_user_try_GET_Players()
    {
        $nonAdminUser = User::where('email', 'RepeatedUser@gmail.com')->first();
        $this->actingAs($nonAdminUser, 'api');

        $response = $this->getJson('/api/players');
        $response->assertStatus(403);
        $response->assertJson(['error' => 'Esta acción requiere el estatus de administrador']);
    }


    //Aded first and second position clones to check priorities on ranking
    public function test_admin_try_GET_ranking()
    {
        $adminUser = User::where('email', 'Admin@gmail.com')->first();
        $this->actingAs($adminUser, 'api');

        $response = $this->getJson('/api/players/ranking');

        $response->assertStatus(200);
        $response->assertJson([
            ['user_name' => 'User First Position', 'percentage' => 100.00],
            ['user_name' => 'User First Position COPY', 'percentage' => 100.00],
            ['user_name' => 'User SixtySix Percent', 'percentage' => 66.67],
            ['user_name' => 'User Second Position', 'percentage' => 50.00],
            ['user_name' => 'User Second Position COPY', 'percentage' => 50.00],
            ['user_name' => 'RepeatedUser', 'percentage' => 0.00],
            ['user_name' => 'Admin', 'percentage' => 0.00],
            ['user_name' => 'Anonimo', 'percentage' => 0.00],
            ['user_name' => 'User Third Position', 'percentage' => 0.00],
        ]);
    }


    //probamos tanto en caso duplicado como no duplicado, son metodos casi iguales asi que en uno pruebo sin duplicar para no gastar recursos
    public function test_GET_winner_with_seeders()
    {
        $response = $this->getJson('/api/players/ranking/winner');

        $response->assertStatus(200);
        $response->assertJson([
            ['user_name' => 'User First Position', 'percentage' => 100.00],
            ['user_name' => 'User First Position COPY', 'percentage' => 100.00]
        ]);
    }
    public function test_GET_loser_with_seeders()
    {
        $response = $this->getJson('/api/players/ranking/loser');

        $response->assertStatus(200);
        $response->assertJson([
            ['user_name' => 'User Third Position', 'percentage' => 0.00]
        ]);
    }

    //aquí borro la data base para ver que pasa
    public function test_GET_winner_with_no_users()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Game::truncate();
        User::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $response = $this->getJson('/api/players/ranking/winner');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'No users with games']);
    }
 public function test_GET_loser_with_no_users()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Game::truncate();
        User::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $response = $this->getJson('/api/players/ranking/loser');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'No users with games']);
    }

}
