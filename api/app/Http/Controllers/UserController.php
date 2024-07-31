<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    if (Auth::user()->admin_status === 1){
        $users = User::all();
        $games = Game::all();

        //añado arrays a parte para calcular winratios segun el id de cada user
        $ratio = array();
        $ratiopercentage = array();

        foreach ($users as $user) {
            $ratio[$user->id] = ['user_name' => $user->user_name, 'victorias' => [] ];
        }

        foreach ($games as $game) {
            if (isset($ratio[$game->user_id])) {
                $ratio[$game->user_id]['victorias'][] = $game->victoria;
            }
        }

        foreach ($ratio as $user) {
            $totalVictorias = count($user['victorias']);
            $victoriasConValor1 = count(array_filter($user['victorias'], function($v) {
                return $v == 1;
            }));

            $percentage = ($totalVictorias > 0) ? ($victoriasConValor1 / $totalVictorias) * 100 : 0;

            $ratiopercentage[] = [ 'user_name' => $user['user_name'], 'percentage' => $percentage ];
        }

        return response()->json($ratiopercentage, 201);
        }
        else{
            return response()->json(['error'=>'Esta acción requiere el estatus de administrador'], 403);
        }
    }
    //devuelve la media
    public function ranking()
    {
        $users = User::all();
        $games = Game::all();

        $ratio = array();

        foreach ($users as $user) {
            $ratio[$user->id] = [
                'user_name' => $user->user_name,
                'victorias' => []
            ];
        }

        foreach ($games as $game) {
            if (isset($ratio[$game->user_id])) {
                $ratio[$game->user_id]['victorias'][] = $game->victoria;
            }
        }

        $ranking = [];

        foreach ($ratio as $user) {
            $totalVictorias = count($user['victorias']);
            if ($totalVictorias > 0) {
                $victoriasConValor1 = count(array_filter($user['victorias'], function($v) {
                    return $v == 1;
                }));

                $percentage = ($victoriasConValor1 / $totalVictorias) * 100;

                $ranking[] = [
                    'user_name' => $user['user_name'],
                    'percentage' => $percentage
                ];
            }
        }

        usort($ranking, function($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });

        return response()->json($ranking, 200);
    }

//busca al peor jugador, si hay mas de uno muestra a ambos, nunca muestra a un jugador con 0 jugadas
    public function getTheBiggestLoser() {
        $users = User::all();
        $games = Game::all();

        $ratio = array();
        $lowestUsers = [];
        $lowestPercentage = null;

        foreach ($users as $user) {
            $ratio[$user->id] = [
                'user_name' => $user->user_name,
                'victorias' => []
            ];
        }

        foreach ($games as $game) {
            if (isset($ratio[$game->user_id])) {
                $ratio[$game->user_id]['victorias'][] = $game->victoria;
            }
        }

        foreach ($ratio as $user) {
            $totalVictorias = count($user['victorias']);
            if ($totalVictorias > 0) {
                $victoriasConValor1 = count(array_filter($user['victorias'], function($v) {
                    return $v == 1;
                }));

                $percentage = ($victoriasConValor1 / $totalVictorias) * 100;

                if ($lowestPercentage === null || $percentage < $lowestPercentage) {
                    $lowestPercentage = $percentage;
                    $lowestUsers = [[
                        'user_name' => $user['user_name'],
                        'percentage' => $percentage
                    ]];
                } elseif ($percentage == $lowestPercentage) {
                    $lowestUsers[] = [
                        'user_name' => $user['user_name'],
                        'percentage' => $percentage
                    ];
                }
            }
        }

        if (empty($lowestUsers)) {
            return response()->json(['message' => 'No users with victories'], 404);
        }

        return response()->json($lowestUsers, 201);
    }

    //practicamente la misma logica pero para el user con más victorias, los que no hayan jugado nunca tampoco se tienen en cuenta

    public function getAllUsersWithPercentages() {
        $users = User::all();
        $games = Game::all();

        $ratio = array();
        $ratiopercentage = array();

        foreach ($users as $user) {
            $ratio[$user->id] = [
                'user_name' => $user->name,
                'victorias' => []
            ];
        }

        foreach ($games as $game) {
            if (isset($ratio[$game->user_id])) {
                $ratio[$game->user_id]['victorias'][] = $game->victoria;
            }
        }

        foreach ($ratio as $user) {
            $totalVictorias = count($user['victorias']);
            if ($totalVictorias > 0) {
                $victoriasConValor1 = count(array_filter($user['victorias'], function($v) {
                    return $v == 1;
                }));

                $percentage = ($victoriasConValor1 / $totalVictorias) * 100;

                $ratiopercentage[] = [
                    'user_name' => $user['user_name'],
                    'percentage' => $percentage
                ];
            }
        }

        return response()->json($ratiopercentage, 201);
    }

    public function getTheBiggestWinner() {
        $users = User::all();
        $games = Game::all();

        $ratio = array();
        $highestUsers = [];
        $highestPercentage = null;

        foreach ($users as $user) {
            $ratio[$user->id] = [
                'user_name' => $user->user_name,
                'victorias' => []
            ];
        }

        foreach ($games as $game) {
            if (isset($ratio[$game->user_id])) {
                $ratio[$game->user_id]['victorias'][] = $game->victoria;
            }
        }

        foreach ($ratio as $user) {
            $totalVictorias = count($user['victorias']);
            if ($totalVictorias > 0) {
                $victoriasConValor1 = count(array_filter($user['victorias'], function($v) {
                    return $v == 1;
                }));

                $percentage = ($victoriasConValor1 / $totalVictorias) * 100;

                if ($highestPercentage === null || $percentage > $highestPercentage) {
                    $highestPercentage = $percentage;
                    $highestUsers = [[
                        'user_name' => $user['user_name'],
                        'percentage' => $percentage
                    ]];
                } elseif ($percentage == $highestPercentage) {
                    $highestUsers[] = [
                        'user_name' => $user['user_name'],
                        'percentage' => $percentage
                    ];
                }
            }
        }

        if (empty($highestUsers)) {
            return response()->json(['message' => 'No users with victories'], 404);
        }

        return response()->json($highestUsers, 201);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $validatedData = $request->validate([
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'user_name' => 'nullable|string|max:30',
    ]);

    $userName = $validatedData['user_name'] ?? 'Anonimo';

    if ($userName !== 'Anonimo') {
        $existingUser = User::where('user_name', $userName)->first();
        if ($existingUser) {
            return response()->json(['error' => 'El nombre de usuario ya está en uso.'], 422);
        }
    }

    $user = User::create([
        'email' => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
        'user_name' => $userName,
    ]);

    return response()->json(['message' => 'Usuario creado correctamente', 'user' => $user], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
    if (Auth::user()->id == $id || Auth::user()->admin_status == 1 ){
        $validatedData = $request->validate([
            'user_name' => 'nullable|string|max:30'
           ]);
                $userName = $validatedData['user_name'] ?? 'Anonimo';
                if ($userName === 'Anonimo'){
                    $user = User::findOrFail($id);
                    $user->user_name = 'Anonimo';
                    $user->save();
                        return response()->json(['message' => 'Campo vacío, nombre actualizado a anonimo.', 'user_name'=> $user->user_name], 201);

                }

                else {
                        $existingUser = User::where('user_name', $userName)->first();
                        if ($existingUser) {
                                return response()->json(['error' => 'El nombre de usuario ya está en uso.'], 422);
                        }
                        else{
                            $user = User::findOrFail($id);
                            $user->user_name = $validatedData['user_name'];
                            $user->save();
                                return response()->json(['message' => 'Nombre actualizado correctamente.', 'user_name'=> $user->user_name], 201);
                            }

                    }
            }
        else{
            return response()->json('No estas autorizado para realizar esta acción.' , 201);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
