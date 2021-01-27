<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PassportAuthController\LoginRequest;
use App\Http\Requests\Api\V1\PassportAuthController\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * Class PassportAuthController
 *
 * @package App\Http\Controllers\Api\V1
 */
class PassportAuthController extends Controller
{
    /**
     * @OA\Post(
     *  path="/api/v1/register",
     *  description="Производится регистрация пользователя",
     *  tags={"Аутентификация пользователя"},
     *  operationId="registerUser",
     *  @OA\Parameter(
     *      name="name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *      @OA\Parameter(
     *      name="password_confirmation",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     * )
     */

    /**
     * @param  RegisterRequest  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ]);

        $token = $user->createToken('LaravelAuthApp')->accessToken;

        return response()->json(['user' => $user, 'token' => $token], 200);
    }

    /**
     * @OA\Post(
     *  path="/api/v1/login",
     *  description="Производится вход в систему пользователя",
     *  tags={"Аутентификация пользователя"},
     *  operationId="loginUser",
     *  @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     * )
     */

    /**
     * @param  LoginRequest  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $data = [
            'email' => $request->get('email'),
            'password' => $request->get('password')
        ];

        if(Auth::attempt($data)) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['user' => auth()->user(), 'token' => $token], 200);
        }

        return response()->json(['errors' => 'Email или пароль не верны'], 401);
    }

    /**
     * @OA\Delete(
     *  path="/api/v1/logout",
     *  description="Производится выход из системы пользователем. Необходимо
        прикреить в заголовоки(headers) Authorization : Bearer token",
     *  tags={"Аутентификация пользователя"},
     *  security={{"bearerAuth":{}}},
     *  operationId="logoutUser",
     *   @OA\Parameter(
     *      name="token",
     *      in="header",
     *      required=true,
     *      @OA\SecurityScheme(
     *          securityScheme="bearerAuth",
     *          type="http",
     *          in="header",
     *          name="Authorization",
     *          scheme="bearer"
     *      ),
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     * )
     */

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request) {
        $request->user()->token()->revoke();
        return response()->json(['successful operation'], 200);
    }
}
