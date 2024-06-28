<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Laravel\Lumen\Routing\Controller as BaseController;
use GuzzleHttp\Client;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseController
{

    use HttpResponseTrait;

    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $authority = 'https://login.microsoftonline.com';
    protected $tenant;
    public function __construct()
{
    $this->clientId = env('CLIENT_ID');
    $this->clientSecret = env('CLIENT_SECRET');
    $this->redirectUri = env('REDIRECT_URI');
    $this->tenant = env('TENANT_ID');
}

    public function redirectToProvider()
    {
        $authorizationUrl = "{$this->authority}/{$this->tenant}/oauth2/v2.0/authorize?" . http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'response_mode' => 'query',
            'scope' => 'openid profile email offline_access user.read',
        ]);

        return redirect($authorizationUrl);
    }

    public function handleProviderCallback(Request $request)
    {
        $code = $request->input('code');

        if (!$code) {
            return response()->json(['error' => 'Authorization code not found'], 400);
        }

        $tokenUrl = "{$this->authority}/{$this->tenant}/oauth2/v2.0/token";

        $http = new Client;

        $response = $http->post($tokenUrl, [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'grant_type' => 'authorization_code',
            ],
        ]);

        $tokens = json_decode((string) $response->getBody(), true);

        // Manejar los tokens como sea necesario, p.ej., almacenar en la base de datos, establecer en sesión, etc.
        return response()->json($tokens);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => app('hash')->make($request->password),
        ]);

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

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->error('Invalid credentials', [], Response::HTTP_UNAUTHORIZED);
        }

        $user = JWTAuth::user();
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

    public function logout(){
        JWTAuth::parseToken()->authenticate();
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->success("User logged out successfully", []);     
    }

    public function me() {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return $this->error('User not found', [], Response::HTTP_NOT_FOUND);
        }

        return $this->success("User obtained", ['user' => $user]);
    }

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
