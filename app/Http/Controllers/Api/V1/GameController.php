<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $game = Game::whereId($id)
            ->with(
                'tournament',
                'firstTeam',
                'secondTeam',
                'videoSource',
                'textSource',
                'pressConferences',
                'comments'
            )
            ->firstOrFail();
        return response()->json($game, 200);
    }
}
