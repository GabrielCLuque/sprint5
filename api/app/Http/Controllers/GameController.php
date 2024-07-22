<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => "No se ha encontrado ningún usuario con esas credenciales"], 404);
        }

        $games = Game::where('user_id', $id)->get();

        if ($games->isEmpty()) {
            return response()->json(['message' => 'Este usuario no ha jugado ninguna partida todavía'], 404);
        }

        $games = $games->map(function($game) {
            return [
                'id' => $game->id,
                'user_id' => $game->user_id,
                'resultado_tirada_1' => $game->resultado_tirada_1,
                'resultado_tirada_2' => $game->resultado_tirada_2,
                'resultado_final' => $game->resultado_final,
                'victoria' => $game->victoria ? 'ganado' : 'perdido',
                'created_at' => $game->created_at,
                'updated_at' => $game->updated_at
            ];
        });

        return response()->json($games, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game  $game)
    {
        //
    }
}
