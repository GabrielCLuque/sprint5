<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Laravel\Passport\ClientRepository;

class TokensTestSeeder extends Seeder
{
    public function run()
    {

        $clientRepository = new ClientRepository();

        if (!$clientRepository->personalAccessClient()) {
            $clientRepository->createPersonalAccessClient(
                null,
                'Personal Access Client',
                'http://localhost'
            );
        }

        // Tokens for admin and not admin
        $users = User::whereIn('email', ['Admin@gmail.com', 'anonimo@gmail.com'])->get();

        foreach ($users as $user) {
            $tokenResult = $user->createToken('TestToken');
            $token = $tokenResult->accessToken;

            $user->test_token = $token;
            $user->save();

        }
    }
}
