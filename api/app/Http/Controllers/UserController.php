<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
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

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $validatedData = $request->validate([
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'user_name' => 'nullable|string|max:255',
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
        'password' => bcrypt($validatedData['password']),
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
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
