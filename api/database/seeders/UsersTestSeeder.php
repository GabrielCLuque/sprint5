<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::create([
            'email' => 'RepeatedUser@gmail.com',
            'password' => Hash::make('password123'),
            'user_name' => 'RepeatedUser',
            'admin_status' => 0,
        ]);

        User::create([
            'email' => 'Admin@gmail.com',
            'password' => Hash::make('password123'),
            'user_name' => 'Admin',
            'admin_status' => 1,
        ]);

        User::create([
            'email' => 'anonimo@gmail.com',
            'password' => Hash::make('password123'),
            'user_name' => 'Anonimo',
            'admin_status' => 0,
        ]);

        // Users for ranking examples
        User::create([
            'email' => 'firstposition@gmail.com',
            'password' => Hash::make('password123'),
            'user_name' => 'User First Position',
            'admin_status' => 0,
        ]);

        User::create([
            'email' => 'firstpositionCOPY@gmail.com',
            'password' => Hash::make('password123'),
            'user_name' => 'User First Position COPY',
            'admin_status' => 0,
        ]);


        User::create([
            'email' => 'secondposition@gmail.com',
            'password' => Hash::make('password123'),
            'user_name' => 'User Second Position',
            'admin_status' => 0,
        ]);

        User::create([
            'email' => 'secondpositionCOPY@gmail.com',
            'password' => Hash::make('password123'),
            'user_name' => 'User Second Position COPY',
            'admin_status' => 0,
        ]);


        User::create([
            'email' => 'thirdposition@gmail.com',
            'password' => Hash::make('password123'),
            'user_name' => 'User Third Position',
            'admin_status' => 0,
        ]);
        User::create([
            'email' => 'sixtysixpercent@gmail.com',
            'password' => Hash::make('password123'),
            'user_name' => 'User SixtySix Percent',
            'admin_status' => 0,
        ]);


    }
}
