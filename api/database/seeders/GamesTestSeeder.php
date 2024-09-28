<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\User;

class GamesTestSeeder extends Seeder
{
    public function run()
    {
        $firstPositionUser = User::where('email', 'firstposition@gmail.com')->first();
        $firstPositionUserCOPY = User::where('email', 'firstpositionCOPY@gmail.com')->first();
        $secondPositionUser = User::where('email', 'secondposition@gmail.com')->first();
        $secondPositionUserCOPY = User::where('email', 'secondpositionCOPY@gmail.com')->first();
        $thirdPositionUser = User::where('email', 'thirdposition@gmail.com')->first();

        //  First Position" 100%
        Game::create([
            'user_id' => $firstPositionUser->id,
            'resultado_tirada_1' => 7,
            'resultado_tirada_2' => 0,
            'resultado_final' => 11,
            'victoria' => true,
        ]);
        Game::create([
            'user_id' => $firstPositionUser->id,
            'resultado_tirada_1' => 3,
            'resultado_tirada_2' => 4,
            'resultado_final' => 7,
            'victoria' => true,
        ]);


        Game::create([
            'user_id' => $firstPositionUserCOPY->id,
            'resultado_tirada_1' => 7,
            'resultado_tirada_2' => 0,
            'resultado_final' => 7,
            'victoria' => true,
        ]);
        Game::create([
            'user_id' => $firstPositionUserCOPY->id,
            'resultado_tirada_1' => 3,
            'resultado_tirada_2' => 4,
            'resultado_final' => 7,
            'victoria' => true,
        ]);







        // Second Position" 50%
        Game::create([
            'user_id' => $secondPositionUser->id,
            'resultado_tirada_1' => 5,
            'resultado_tirada_2' => 6,
            'resultado_final' => 11,
            'victoria' => true,
        ]);
        Game::create([
            'user_id' => $secondPositionUser->id,
            'resultado_tirada_1' => 1,
            'resultado_tirada_2' => 3,
            'resultado_final' => 4,
            'victoria' => false,
        ]);

        Game::create([
            'user_id' => $secondPositionUserCOPY->id,
            'resultado_tirada_1' => 5,
            'resultado_tirada_2' => 6,
            'resultado_final' => 11,
            'victoria' => true,
        ]);
        Game::create([
            'user_id' => $secondPositionUserCOPY->id,
            'resultado_tirada_1' => 1,
            'resultado_tirada_2' => 3,
            'resultado_final' => 4,
            'victoria' => false,
        ]);



        // User Third Position 0%
        Game::create([
            'user_id' => $thirdPositionUser->id,
            'resultado_tirada_1' => 2,
            'resultado_tirada_2' => 3,
            'resultado_final' => 5,
            'victoria' => false,
        ]);
        Game::create([
            'user_id' => $thirdPositionUser->id,
            'resultado_tirada_1' => 3,
            'resultado_tirada_2' => 4,
            'resultado_final' => 7,
            'victoria' => false,
        ]);


        //percentagecase
        $sixtySixPercentUser = User::where('email', 'sixtysixpercent@gmail.com')->first();

        Game::create([
            'user_id' => $sixtySixPercentUser->id,
            'resultado_tirada_1' => 3,
            'resultado_tirada_2' => 4,
            'resultado_final' => 7,
            'victoria' => true,
        ]);

        Game::create([
            'user_id' => $sixtySixPercentUser->id,
            'resultado_tirada_1' => 5,
            'resultado_tirada_2' => 6,
            'resultado_final' => 11,
            'victoria' => false,
        ]);

        Game::create([
            'user_id' => $sixtySixPercentUser->id,
            'resultado_tirada_1' => 3,
            'resultado_tirada_2' => 4,
            'resultado_final' => 7,
            'victoria' => true,
        ]);

    }
}
