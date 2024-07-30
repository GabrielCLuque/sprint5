<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function store($user_id)
    {
        if (Auth::user()->id == $user_id){
        $resultado_tirada_1 = rand(1, 6);
        $resultado_tirada_2 = rand(1, 6);
        $resultado_final = $resultado_tirada_1 + $resultado_tirada_2;
        $victoria = $resultado_final == 7 ? 1 : 0;

        $game = new Game();
        $game->user_id = $user_id;
        $game->resultado_tirada_1 = $resultado_tirada_1;
        $game->resultado_tirada_2 = $resultado_tirada_2;
        $game->resultado_final = $resultado_final;
        $game->victoria = $victoria;
        $game->save();

        $mensaje = "Resultado primer dado: $resultado_tirada_1, ";
        $mensaje .= "Resultado segundo dado: $resultado_tirada_2, ";
        $mensaje .= "Resultado final: $resultado_final, ";
        $mensaje .= $victoria ? "Has ganado" : "Has perdido";

        return response()->json(['message' => $mensaje], 201);
        }
        else{
            return response()->json('No puedes jugar partidas como otro usuario' , 201);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        if (Auth::user()->admin_status === 1){
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
        else{
            return response()->json(['error'=>'Esta acción requiere el estatus de administrador'], 403);
        }
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
