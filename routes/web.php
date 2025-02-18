<?php

use App\Http\Controllers\DisplayController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperatorController;

Route::get('/', [DisplayController::class, 'index']);
Route::get('/operator', [OperatorController::class, 'view']);
Route::get('/operator/matches', [OperatorController::class, 'getMatches']);
Route::post('/operator/matches', [OperatorController::class, 'createMatch']);
Route::post('/operator/matches/{game_match_id}/update-score', [OperatorController::class, 'updateScore']);
Route::post('/operator/matches/{game_match_id}/finish', [OperatorController::class, 'finishMatch']);
Route::get('/operator/matches/{game_match_id}/history', [OperatorController::class, 'getMatchHistory']);
