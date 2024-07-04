<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Services\Impl\UserServiceImpl;
use App\Traits\HttpResponseTrait;
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

        return $this->success("User created succesfully", 
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
            return $this->error('Invalid credentials', [], Response::HTTP_UNAUTHORIZED);
        }

        $user = JWTAuth::user();
        if(!$user->active){
            return $this->error('User is deactivated', [], Response::HTTP_FORBIDDEN);
        }

        $refreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user);
        $expiresIn = auth('api')->factory()->getTTL() * 60;

        return $this->success("User logged in successfully", 
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
        return $this->success("User logged out successfully", []);     
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
            return $this->error('User not found', [], Response::HTTP_NOT_FOUND);
        }

        return $this->success("User obtained", ['user' => $user]);
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

        return $this->success("Token refreshed successfully", 
            [
                'refresh_token' => $newRefreshToken,
                'token' => $newToken, 
                'expires_in' => $newExpiresIn,
                'token_type' => 'bearer',
            ]);
    }
}
