<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\Request;

/**
 * Class TournamentController
 *
 * @package App\Http\Controllers\Api\V1
 */
class TournamentController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/v1/tournament-list/{status}",
     *  description="Получение списка турниров, может быть regular(чемпионаты), cup(кубки)",
     *  tags={"Список турниров"},
     *  operationId="getTournamentList",
     *     @OA\Parameter(
     *         description="Статус турнира",
     *         in="path",
     *         name="status",
     *         required=true,
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *  @OA\Response(
     *      response=200,
     *      description="successful operation",
     *  ),
     * )
     */

    /**
     * @param  string  $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexList(string $status)
    {
        $tournament = Tournament::where('status', $status)->get();

        return response()->json($tournament, 200);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $tournament = Tournament::where('id', $id)->firstOrFail();

        return response()->json($tournament, 200);
    }
}
