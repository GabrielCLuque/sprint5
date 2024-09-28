<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UsersUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();



        if (!file_exists(storage_path('oauth-private.key')) || !file_exists(storage_path('oauth-public.key'))) {
            \Artisan::call('passport:keys');
        }
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => \Database\Seeders\UsersTestSeeder::class]);

    }

    public function test_user_can_change_own_name()
    {
        $user = User::where('email', 'firstposition@gmail.com')->first();
        Passport::actingAs($user);

        $response = $this->putJson("/api/players/{$user->id}", [
            'user_name' => 'NuevoNombre',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Nombre actualizado correctamente.', 'user_name' => 'NuevoNombre']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'user_name' => 'NuevoNombre']);
    }

    public function test_admin_can_change_any_users_name()
    {
        $admin = User::where('email', 'Admin@gmail.com')->first();
        $user = User::where('email', 'secondposition@gmail.com')->first();
        Passport::actingAs($admin);

        $response = $this->putJson("/api/players/{$user->id}", [
            'user_name' => 'NombreCambiadoPorAdmin',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Nombre actualizado correctamente.', 'user_name' => 'NombreCambiadoPorAdmin']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'user_name' => 'NombreCambiadoPorAdmin']);
    }

    public function test_user_cannot_change_name_to_duplicate_name_except_anonimo()
    {
        $user = User::where('email', 'firstposition@gmail.com')->first();
        Passport::actingAs($user);

        $response = $this->putJson("/api/players/{$user->id}", [
            'user_name' => 'Anonimo',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Nombre actualizado correctamente.', 'user_name' => 'Anonimo']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'user_name' => 'Anonimo']);
    }

    public function test_user_cannot_change_name_to_existing_name()
    {
        $user = User::where('email', 'firstposition@gmail.com')->first();
        $otherUser = User::where('email', 'secondposition@gmail.com')->first();
        Passport::actingAs($user);

        $response = $this->putJson("/api/players/{$user->id}", [
            'user_name' => $otherUser->user_name,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['error' => 'El nombre de usuario ya está en uso.']);
    }

    public function test_user_can_change_name_to_anonimo_if_name_field_is_empty()
    {
        $user = User::where('email', 'firstposition@gmail.com')->first();
        Passport::actingAs($user);

        $response = $this->putJson("/api/players/{$user->id}", [
            'user_name' => '',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Campo vacío, nombre actualizado a Anonimo.', 'user_name' => 'Anonimo']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'user_name' => 'Anonimo']);
    }
    public function test_non_admin_user_cannot_change_another_users_name()
{
    $user = User::where('email', 'firstposition@gmail.com')->first();
    $otherUser = User::where('email', 'secondposition@gmail.com')->first();
    Passport::actingAs($user);

    $response = $this->putJson("/api/players/{$otherUser->id}", [
        'user_name' => 'NuevoNombre',
    ]);

    $response->assertStatus(403);
    $this->assertDatabaseHas('users', ['id' => $otherUser->id, 'user_name' => $otherUser->user_name]);
}
public function test_cannot_update_non_existent_user()
{
    $admin = User::where('email', 'Admin@gmail.com')->first();
    Passport::actingAs($admin);

    $response = $this->putJson("/api/players/99999", [
        'user_name' => 'NombreInexistente',
    ]);

    $response->assertStatus(404);
}
public function test_user_name_cannot_be_only_spaces()
{
    $user = User::where('email', 'firstposition@gmail.com')->first();
    Passport::actingAs($user);

    $response = $this->putJson("/api/players/{$user->id}", [
        'user_name' => '   ',
    ]);

    $response->assertStatus(201);
    $response->assertJson(['message' => 'Campo vacío, nombre actualizado a Anonimo.', 'user_name' => 'Anonimo']);
    $this->assertDatabaseHas('users', ['id' => $user->id, 'user_name' => 'Anonimo']);
}


public function test_user_cannot_change_name_to_case_insensitive_duplicate()
{
    $user = User::where('email', 'firstposition@gmail.com')->first();
    Passport::actingAs($user);

    $response = $this->putJson("/api/players/{$user->id}", [
        'user_name' => 'USER SECOND POSITION',
    ]);

    $response->assertStatus(422);
    $response->assertJson(['error' => 'El nombre de usuario ya está en uso.']);
}


}
