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
$router->get('/login', 'AuthController@redirectToProvider');
$router->get('/callback', 'AuthController@handleProviderCallback');


//Ejemplo de lo que actualmente se envia para el "restore"
$router->get('/project/{id}', 'ProjectController@show');

$router->post('/role','RoleController@createRole');


//RUTAS PROTEGIDAS
$router->group( ['middleware' => 'auth'], function () use ($router) {

    $router->get('/rutatest', function () use ($router) {
        return $router->app->version();
    });

});
