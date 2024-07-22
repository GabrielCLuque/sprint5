<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/players', [UserController::class, 'store']);

Route::put('/players/{id}', [UserController::class, 'update']);

Route::post('/players/{id}/games', [GameController::class, 'create']);

Route::delete('/players/{id}/games', [GameController::class, 'delete']);

Route::get('/players', [UserController::class, 'index']);

Route::get('/players/{id}/games', [GameController::class, 'show']);

Route::get('/players/ranking', [UserController::class, 'getAverageRanking']);

Route::get('/players/ranking/loser', [UserController::class, 'getTheBiggestLoser']);

Route::get('/players/ranking/winner', [UserController::class, 'getTheBiggestWinner']);

Route::post('login', [AuthController::class, 'login']);
