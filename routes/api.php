<?php

use App\Http\Controllers\Api\V1\PassportAuthController;
use App\Http\Controllers\Api\V1\PressConferenceController;
use App\Http\Controllers\Api\V1\TournamentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/v1'], function() {
    Route::post('register', [PassportAuthController::class, 'register']);
    Route::post('login', [PassportAuthController::class, 'login']);
    Route::get('press-conferences/{count}', [PressConferenceController::class, 'index']);
    Route::get('tournament-list/{status}', [TournamentController::class, 'indexList']);
    Route::get('press-conferences/tournament_id/{tournament_id}/count/{count}', [PressConferenceController::class, 'indexTournament']);
});
