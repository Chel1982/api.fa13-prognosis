<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PressConference;
use Illuminate\Http\Request;

class PressConferenceController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/v1/press-conferences",
     *  description="Получение последних 10 пресс - конференций",
     *  tags={"Пресс - конференции"},
     *  operationId="getPressConferenes",
     *  @OA\Response(
     *      response=200,
     *      description="successful operation",
     *      @OA\MediaType(
     *          mediaType="application/json",
     *      )
     *  ),
     * )
     */

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $pressConference = PressConference::latest('date')
            ->with('game', 'game.tournament', 'game.firstTeam', 'game.secondTeam', 'game.videoSource', 'game.textSource')
            ->paginate(10);

        return response()->json($pressConference, 200);
    }
}
