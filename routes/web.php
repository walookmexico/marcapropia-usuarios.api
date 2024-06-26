<?php

use App\Http\Controllers\ProjectController;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/api/documentation', function () {
    return view('vendor.l5-swagger.index');
});

// routes/web.php
//$router->get('/login', 'AuthController@redirectToProvider');
//$router->get('/callback', 'AuthController@handleProviderCallback');

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('/users/register', 'AuthController@register');
    $router->post('/users/login', 'AuthController@login');
    $router->post('/users/logout', 'AuthController@logout');

    $router->post('/users/refresh', 'AuthController@refreshToken');
    $router->get('/users/me', 'AuthController@me');
});

$router->post('/role','RoleController@createRole');
