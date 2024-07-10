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
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string", format="password")
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response="201", description="User created successfully")
     * )
     */
    public function register(Request $request)
    {
        $registerUserRequest = new RegisterUserRequest();
        $registerUserRequest->validate($request);

        $user = $this->userService->registerUser($request->all());
       
        $token = JWTAuth::fromUser($user);
        $expiresIn = auth('api')->factory()->getTTL() * 60;
        $refreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user); 

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
     *     summary="Log in with credentials",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response="200", description="User logged in successfully"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->error(trans('user.invalid_credentials'), [], Response::HTTP_UNAUTHORIZED);
        }

        $user = JWTAuth::user();
        if(!$user->active){
            return $this->error(trans('user.user_already_deactivated'), [], Response::HTTP_FORBIDDEN);
        }

        $refreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user);
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
     *     summary="Log out user",
     *     @OA\Response(response="200", description="User logged out successfully")
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
     *     summary="Get current user information",
     *     @OA\Response(response="200", description="User obtained"),
     *     @OA\Response(response="404", description="User not found")
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
     *     summary="Refresh JWT Token",
     *     @OA\Response(response="200", description="Token refreshed successfully")
     * )
     */
    public function refreshToken(){ 

        $user = JWTAuth::parseToken()->authenticate();
        $refreshToken = JWTAuth::getToken();
        $newToken = JWTAuth::refresh($refreshToken);
        $newRefreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user);
        $newExpiresIn = auth('api')->factory()->getTTL() * 60;

        return $this->success(trans('token.token_refreshed'), 
            [
                'refresh_token' => $newRefreshToken,
                'token' => $newToken, 
                'expires_in' => $newExpiresIn,
                'token_type' => 'bearer',
            ]);
    }

    public function getUser($id){
        try {
            $user = $this->userService->getUserWithAllRelationsById($id);
            return $this->success(trans('user.user_retrieved'), ['user' => $user]);
        } catch (ModelNotFoundException $e) {
            return $this->error(trans('user.user_not_found'), [], Response::HTTP_NOT_FOUND);
        }
    }
    public function getAllUser(Request $request){
        $perPage = $request->input('per_page', 10);
        $searchBy = $request->input('searchBy', 'name');
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
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
