<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use GuzzleHttp\Client;

class AuthController extends BaseController
{
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
}
