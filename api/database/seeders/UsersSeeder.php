<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::create([
            'email' => 'adminuser@gmail.com',
            'password' => Hash::make('adminpassword'), // Password hashing
            'user_name' => 'AdminUser', // This uses the 'user_name' field as defined in your migration
            'admin_status' => 1, // Admin user
        ]);

        // Create a regular user
        User::create([
            'email' => 'regularuser@gmail.com',
            'password' => Hash::make('userpassword'),
            'user_name' => 'RegularUser',
            'admin_status' => 0, // Regular user
        ]);
    }
}
