<?php

use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\GameController;
use App\Http\Controllers\Api\V1\PassportAuthController;
use App\Http\Controllers\Api\V1\PressConferenceController;
use App\Http\Controllers\Api\V1\TournamentController;
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

Route::group(['prefix' => '/v1'], function() {
    Route::get('press-conferences/count/{count}', [PressConferenceController::class, 'index']);
    Route::get('tournament-list/{status}', [TournamentController::class, 'indexList']);
    Route::get('press-conferences/tournament_id/{tournament_id}/count/{count}', [PressConferenceController::class, 'indexTournament']);
    Route::get('game/id/{id}', [GameController::class, 'show']);

    Route::post('comment/game/', [CommentController::class, 'store'])->middleware('auth:api');
    Route::get('comments/game/{id}', [CommentController::class, 'getCommentsByGame']);

    Route::post('login', [PassportAuthController::class, 'login']);
    Route::post('register', [PassportAuthController::class, 'register']);
    Route::get('user', [PassportAuthController::class, 'auth'])->middleware('auth:api');
    Route::delete('logout', [PassportAuthController::class, 'logout'])->middleware('auth:api');
});
