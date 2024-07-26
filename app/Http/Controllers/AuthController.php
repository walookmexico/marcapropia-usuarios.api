<?php

namespace App\Http\Controllers;

use App\Exceptions\UserActivatedException;
use App\Exceptions\UserDeactivatedException;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\Impl\UserServiceImpl;
use App\Traits\HttpResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    use HttpResponseTrait;

    protected $userService;

    /**
     * AuthController constructor.
     */
    public function __construct(){
        $this->userService = UserServiceImpl::getInstance();
    }

    /**
     * @OA\Post(
     *     path="/api/users/register",
     *     summary="Registra un usuario",
     *     tags={"usuarios"},
     *     operationId="users-register",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="fullName", type="string", example="Juan Borges"),
     *             @OA\Property(property="email", type="string", example="jborges@soriana.com"),
     *             @OA\Property(property="password", type="string", example="Wlk12345678"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="Wlk12345678"),
     *             @OA\Property(property="company", type="string", example="Walook"),
     *             @OA\Property(property="userType", type="integer", example=50),
     *             @OA\Property(property="areaCode", type="string", nullable=true, example=null),
     *             @OA\Property(property="phone", type="string", example="12"),
     *             @OA\Property(property="job", type="integer", example=0),
     *             @OA\Property(property="employeeCode", type="string", example="12345")
     *         )
     *     ),
     *     @OA\Response(
     *          response="201",
     *          description="Usuario creado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="refresh_token", type="string", example="refreh_token"),
     *                 @OA\Property(property="token", type="string", example="token"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *                 @OA\Property(property="message", type="string", example="Usuario creado correctamente"),
     *                 @OA\Property(property="code", type="integer", example=201)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en los campos",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object"),
     *                 @OA\Property(property="message", type="string", example="Error en los campos"),
     *                 @OA\Property(property="code", type="integer", example=400)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function register(Request $request){
        $registerUserRequest = new RegisterUserRequest();
        $registerUserRequest->validate($request);

        $user = $this->userService->registerUser($request->all());

        $token = JWTAuth::claims([
            'iss' => env('JWT_ISS'), // Establecer el emisor (Issuer) para el token de acceso
            'aud' => env('JWT_AUD'), // Establecer el receptor (Audience) para el token de acceso
        ])->fromUser($user);
        $expiresIn = auth('api')->factory()->getTTL() * 60;
        $refreshToken = JWTAuth::claims([
            'type' => 'refresh',
            'iss' => env('JWT_ISS'), // Establecer el emisor (Issuer) para el token de acceso
            'aud' => env('JWT_AUD'), // Establecer el receptor (Audience) para el token de acceso
        ])->fromUser($user);

        return $this->success(trans('user.user_created'),
        [
            'refresh_token' => $refreshToken,
            'token' => $token,
            'expires_in' => $expiresIn,
            'token_type' => 'bearer',
            'user' => $user,
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *     path="/api/users/login",
     *     summary="Inicia sesión del usuario",
     *     tags={"usuarios"},
     *     operationId="users-login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="Juan Borges"),
     *             @OA\Property(property="password", type="string", format="password", example="Wlk12345678")
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Usuario ha iniciado sesión correctamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="refresh_token", type="string", example="refreh_token"),
     *                  @OA\Property(property="token", type="string", example="token"),
     *                  @OA\Property(property="expires_in", type="integer", example=3600),
     *                  @OA\Property(property="token_type", type="string", example="bearer")
     *              ),
     *              @OA\Property(property="message", type="string", example="Usuario ha iniciado sesión correctamente"),
     *              @OA\Property(property="code", type="integer", example=200)
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Credenciales inválidas",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Credenciales inválidas"),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::claims([
            'iss' => env('JWT_ISS'), // Establecer el emisor (Issuer) para el token de acceso
            'aud' => env('JWT_AUD'), // Establecer el receptor (Audience) para el token de acceso
        ])->attempt($credentials)) {
            return $this->error(trans('user.invalid_credentials'), [], Response::HTTP_UNAUTHORIZED);
        }

        $user = JWTAuth::user();
        if(!$user->active){
            return $this->error(trans('user.user_already_deactivated'), [], Response::HTTP_FORBIDDEN);
        }

        $refreshToken = JWTAuth::claims([
            'type' => 'refresh',
            'iss' => env('JWT_ISS'), // Establecer el emisor (Issuer) para el token de acceso
            'aud' => env('JWT_AUD'), // Establecer el receptor (Audience) para el token de acceso
        ])->fromUser($user);
        $expiresIn = auth('api')->factory()->getTTL() * 60;

        return $this->success(trans('user.user_logged_in'),
            [
                'refresh_token' => $refreshToken,
                'token' => $token,
                'expires_in' => $expiresIn,
                'token_type' => 'bearer'
            ]);
    }

    /**
     * @OA\Post(
     *     path="/api/users/logout",
     *     summary="Cierra sesión del usuario",
     *     tags={"usuarios"},
     *     operationId="users-logout",
     *     @OA\Response(
     *          response="200",
     *          description="Usuario ha cerrado sesión correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Usuario ha cerrado sesión correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Token expirado/Token inválido/Token no presente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Token expirado/Token inválido/Token no presente"),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function logout(){
        JWTAuth::parseToken()->authenticate();
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->success(trans('user.user_logged_out'), []);
    }

    /**
     * @OA\Get(
     *     path="/api/users/me",
     *     summary="Obtiene un usuario mediante el token",
     *     tags={"usuarios"},
     *     operationId="users-me",
     *     @OA\Response(
     *          response="200",
     *          description="Usuario recuperado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *             ),
     *             @OA\Property(property="message", type="string", example="Usuario obtenido correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Token expirado/Token inválido/Token no presente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Token expirado/Token inválido/Token no presente"),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Usuario no encontrado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Usuario no encontrado"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function me() {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return $this->error(trans('user.user_not_found'), [], Response::HTTP_NOT_FOUND);
        }

        return $this->success(trans('user.user_obtained'), ['user' => $user]);
    }

    /**
     * @OA\Get(
     *     path="/api/users/refresh",
     *     summary="Refresca el token del usuario",
     *     tags={"usuarios"},
     *     operationId="users-refresh",
     *     @OA\Response(
     *          response="200",
     *          description="Token refrescado correctamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="refresh_token", type="string"),
     *                  @OA\Property(property="token", type="string"),
     *                  @OA\Property(property="expires_in", type="integer"),
     *                  @OA\Property(property="token_type", type="string")
     *              ),
     *              @OA\Property(property="message", type="string", example="Token refrescado correctamente"),
     *              @OA\Property(property="code", type="integer", example=200)
     *          )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Token expirado/Token inválido/Token no presente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Token expirado/Token inválido/Token no presente"),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function refreshToken(){

        $user = JWTAuth::parseToken()->authenticate();
        $refreshToken = JWTAuth::getToken();
        $newToken = JWTAuth::claims([
            'iss' => env('JWT_ISS'), // Establecer el emisor (Issuer) para el token de acceso
            'aud' => env('JWT_AUD'), // Establecer el receptor (Audience) para el token de acceso
        ])->refresh($refreshToken);
        $newRefreshToken = JWTAuth::claims([
            'type' => 'refresh',
            'iss' => env('JWT_ISS'), // Establecer el emisor (Issuer) para el token de acceso
            'aud' => env('JWT_AUD'), // Establecer el receptor (Audience) para el token de acceso
        ])->fromUser($user);
        $newExpiresIn = auth('api')->factory()->getTTL() * 60;

        return $this->success(trans('token.token_refreshed'),
            [
                'refresh_token' => $newRefreshToken,
                'token' => $newToken,
                'expires_in' => $newExpiresIn,
                'token_type' => 'bearer',
            ]);
    }

     /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Obtiene un usuario",
     *     tags={"usuarios"},
     *     operationId="users-retrieved",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Usuario recuperado correctamente",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *             ),
     *             @OA\Property(property="message", type="string", example="Usuario recuperado correctamente"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Usuario no encontrado",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Usuario no encontrado"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Ocurrió un error inesperado en el servidor. Por favor, inténtelo de nuevo más tarde o contacte con soporte si el problema persiste"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function getUser($id){
        try {
            $user = $this->userService->getUserWithAllRelationsById($id);
            return $this->success(trans('user.user_retrieved'), ['user' => $user]);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('user.user_not_found'), [], Response::HTTP_NOT_FOUND);
        }
    }
    public function getAllUser(Request $request){
        $perPage = $request->input('perPage', 10);
        $searchBy = $request->input('searchBy', 'name');
        $search = $request->input('search');
        $sortBy = $request->input('sortBy', 'id');
        $sortDirection = $request->input('sortDirection', 'asc');
        $userPaginated = $this->userService->getUsersPaginated($perPage, $searchBy, $search, $sortBy, $sortDirection);
        return $this->success(trans('user.users_retrieved'), ['pagination' => $userPaginated]);
    }


    public function updateUser(Request $request, $id){
        try {
            UpdateUserRequest::validate($request, $id);
            $user = $this->userService->updateUser($id, $request->all());
            return $this->success(trans('user.user_updated'), ['user' => $user]);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('user.user_not_found'), [], Response::HTTP_NOT_FOUND);
        }
    }

    public function deactivateUser($id){
        try {
            $user = $this->userService->getUserById($id);
            if(!$user->active){
                throw new UserDeactivatedException();
            }

            $this->userService->deactivateUser($id);
            return $this->success(trans('user.user_deactivated'));
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('user.user_not_found'), [], Response::HTTP_NOT_FOUND);
        } catch (UserDeactivatedException $e) {
            return $this->error(trans('user.user_already_deactivated'), [], Response::HTTP_CONFLICT);
        }
    }

    public function activateUser($id){
        try {
            $user = $this->userService->getUserById($id);
            if($user->active){
                throw new UserActivatedException();
            }

            $this->userService->activateUser($id);

            return $this->success(trans('user.user_activated'));
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('user.user_not_found'), [], Response::HTTP_NOT_FOUND);
        } catch (UserActivatedException $e) {
            return $this->error(trans('user.user_already_activated'), [], Response::HTTP_CONFLICT);
        }
    }
}
