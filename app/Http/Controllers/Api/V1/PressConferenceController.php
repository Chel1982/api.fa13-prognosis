<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\PressConference;
use App\Models\Tournament;
use Illuminate\Support\Facades\DB;

/**
 * Class PressConferenceController
 *
 * @package App\Http\Controllers\Api\V1
 */
class PressConferenceController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/v1/press-conferences/{count}",
     *  description="Получение последних пресс - конференций. Максимальное количество
        за 1 запрос = 100, если больше 100, то вернеться 1 пресс-конференция"
        Пагинация работает по правилам laravel,
     *  tags={"Пресс - конференции"},
     *  operationId="getAllLastPressConferenes",
     *     @OA\Parameter(
     *         description="Количество полученных пресс-конференций",
     *         in="path",
     *         name="count",
     *         required=true,
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *  @OA\Response(
     *      response=200,
     *      description="successful operation",
     *  ),
     * )
     */

    /**
     * @param  int  $count
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $count)
    {
        $count = $count < 101 ? $count : 1;

        $pressConference = PressConference::latest('date')
            ->with(
                'game',
                'game.tournament',
                'game.firstTeam',
                'game.secondTeam',
                'game.videoSource',
                'game.textSource')
            ->paginate($count);

        return response()->json($pressConference, 200);
    }

    /**
     * @OA\Get(
     *  path="/api/v1/press-conferences/tournament_id/{tournament_id}/count/{count}",
     *  description="Получение последних пресс - конференций определенного турнира. Максимальное количество
        за 1 запрос = 100, если больше 100, то вернеться 1 пресс-конференция.
        Пагинация работает по правилам laravel",
     *  tags={"Пресс - конференции"},
     *  operationId="getTurnamentLastPressConferenes",
     *     @OA\Parameter(
     *         description="ID'шник турнира",
     *         in="path",
     *         name="tournament_id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Количество полученных пресс-конференций",
     *         in="path",
     *         name="count",
     *         required=true,
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *  @OA\Response(
     *      response=200,
     *      description="successful operation",
     *  ),
     * )
     */

    /**
     * @param int $tournament_id
     * @param int $count
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexTournament(int $tournament_id, int $count)
    {
        $count = $count < 101 ? $count : 1;

        $pressConference = PressConference::
            join('games', 'press_conferences.game_id', '=', 'games.id')
            ->where('games.tournament_id', '=', $tournament_id)
            ->select('press_conferences.*')
            ->with([
                'game.tournament',
                'game.firstTeam',
                'game.secondTeam',
                'game.videoSource',
                'game.textSource'])
            ->latest('press_conferences.date')
            ->paginate($count);

        return response()->json($pressConference, 200);
    }
}
