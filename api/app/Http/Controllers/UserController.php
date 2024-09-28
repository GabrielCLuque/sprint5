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
    if (Auth::user()->admin_status === 1) {
        $users = User::all();
        $games = Game::all();

        $ratio = array();
        $ratiopercentage = array();

        foreach ($users as $user) {
            $ratio[$user->id] = ['user_name' => $user->user_name, 'victorias' => []];
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

            $percentage = ($totalVictorias > 0) ? round(($victoriasConValor1 / $totalVictorias) * 100, 2) : 0;

            $ratiopercentage[] = ['user_name' => $user['user_name'], 'percentage' => $percentage];
        }

        return response()->json($ratiopercentage, 200);
    } else {
        return response()->json(['error' => 'Esta acción requiere el estatus de administrador'], 403);
    }
}

    //devuelve la media
    public function ranking()
    {
        $users = User::all();
        $games = Game::all();

        $ratio = [];

        foreach ($users as $user) {
            $ratio[$user->id] = [
                'user_name' => $user->user_name,
                'created_at' => $user->created_at,
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
                $formattedPercentage = number_format($percentage, 2, '.', '');
                //veteranplayers will show up
                $ranking[] = [
                    'user_name' => $user['user_name'],
                    'created_at' => $user['created_at'],
                    'percentage' => floatval($formattedPercentage)
                ];
            } else {
                $ranking[] = [
                    'user_name' => $user['user_name'],
                    'created_at' => $user['created_at'],
                    'percentage' => 0.00
                ];
            }
        }

        usort($ranking, function($a, $b) {
            if ($b['percentage'] == $a['percentage']) {
                return $a['created_at'] <=> $b['created_at'];
            }
            return $b['percentage'] <=> $a['percentage'];
        });

        $ranking = array_map(function ($user) {
            unset($user['created_at']);
            return $user;
        }, $ranking);

        return response()->json($ranking, 200);
    }


//busca al peor jugador, si hay mas de uno muestra a ambos, nunca muestra a un jugador con 0 jugadas


public function getTheBiggestLoser() {
    $users = User::all();
    $games = Game::all();

    $ratio = [];
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
            $formattedPercentage = number_format($percentage, 2, '.', '');

            if ($lowestPercentage === null || $formattedPercentage < $lowestPercentage) {
                $lowestPercentage = $formattedPercentage;
                $lowestUsers = [[
                    'user_name' => $user['user_name'],
                    'percentage' => floatval($formattedPercentage)
                ]];
            } elseif ($formattedPercentage == $lowestPercentage) {
                $lowestUsers[] = [
                    'user_name' => $user['user_name'],
                    'percentage' => floatval($formattedPercentage)
                ];
            }
        }
    }

    if (empty($lowestUsers)) {
        return response()->json(['message' => 'No users with games'], 404);
    }

    return response()->json($lowestUsers, 200);
}
public function getTheBiggestWinner() {
    $users = User::all();
    $games = Game::all();

    $ratio = [];
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
            $formattedPercentage = number_format($percentage, 2, '.', '');

            if ($highestPercentage === null || $formattedPercentage > $highestPercentage) {
                $highestPercentage = $formattedPercentage;
                $highestUsers = [[
                    'user_name' => $user['user_name'],
                    'percentage' => floatval($formattedPercentage)
                ]];
            } elseif ($formattedPercentage == $highestPercentage) {
                $highestUsers[] = [
                    'user_name' => $user['user_name'],
                    'percentage' => floatval($formattedPercentage)
                ];
            }
        }
    }

    if (empty($highestUsers)) {
        return response()->json(['message' => 'No users with games'], 404);
    }

    return response()->json($highestUsers, 200);
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
            'user_name' => [
                'nullable',
                'string',
                'max:30',
                'regex:/^[\pL\pN\s]+$/u' // should only accept regular characters
            ],
        ]);

        $userName = trim($validatedData['user_name'] ?? '');

        if ($userName === '') {
            $userName = 'Anonimo';
        }

        if ($userName !== 'Anonimo') {
            $existingUser = User::where('user_name', $userName)->first();
            if ($existingUser) {
                return response()->json([
                    'errors' => [
                        'user_name' => ['El nombre de usuario ya está en uso.']
                    ]
                ], 422);
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
        if (Auth::user()->id == $id || Auth::user()->admin_status == 1) {
            $validatedData = $request->validate([
                'user_name' => 'nullable|string|max:30'
            ]);

            // case only spaces for name
            $userName = trim($validatedData['user_name'] ?? '');

            if ($userName === '') {
                $userName = 'Anonimo';
                $user = User::findOrFail($id);
                $user->user_name = $userName;
                $user->save();
                return response()->json(['message' => 'Campo vacío, nombre actualizado a Anonimo.', 'user_name' => $user->user_name], 201);
            }

            if ($userName === 'Anonimo') {
                $user = User::findOrFail($id);
                $user->user_name = $userName;
                $user->save();
                return response()->json(['message' => 'Nombre actualizado correctamente.', 'user_name' => $user->user_name], 201);
            }

            $existingUser = User::where('user_name', $userName)->where('id', '!=', $id)->first();
            if ($existingUser) {
                return response()->json(['error' => 'El nombre de usuario ya está en uso.'], 422);
            }

            $user = User::findOrFail($id);
            $user->user_name = $userName;
            $user->save();

            return response()->json(['message' => 'Nombre actualizado correctamente.', 'user_name' => $user->user_name], 201);
        } else {
            return response()->json('No estás autorizado para realizar esta acción.', 403);
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
