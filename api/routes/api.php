<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\API\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/players', [UserController::class, 'store']);

Route::put('/players/{id}', [UserController::class, 'update']);

Route::get('/players/ranking', [UserController::class, 'ranking']);

Route::get('/players/ranking/loser', [UserController::class, 'getTheBiggestLoser']);

Route::get('/players/ranking/winner', [UserController::class, 'getTheBiggestWinner']);

Route::get('/players/{id}/games', [GameController::class, 'show']);

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function() {
    Route::post('/players/{id}/games', [GameController::class, 'store']);

    //admin requests
    Route::get('/players', [UserController::class, 'index']);
    Route::delete('/players/{id}/games', [GameController::class, 'destroy']);
    Route::put('/players/{id}', [UserController::class, 'update']);
});

