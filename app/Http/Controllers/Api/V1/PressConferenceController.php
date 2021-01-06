<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PressConference;

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
           за 1 запрос = 100, если больше 100, то вернеться 1 пресс-конференция",
     *  tags={"Пресс - конференции"},
     *  operationId="getPressConferenes",
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
}
