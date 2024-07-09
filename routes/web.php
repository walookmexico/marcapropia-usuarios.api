<?php

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
    return view('vendor.swagger-lume');
});

// routes/web.php
//$router->get('/login', 'AuthController@redirectToProvider');
//$router->get('/callback', 'AuthController@handleProviderCallback');

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('/users', 'AuthController@getAllUser');
    $router->get('/users/me', 'AuthController@me');
    $router->get('/users/{id}', 'AuthController@getUser');
    $router->post('/users/register', 'AuthController@register');
    $router->put('/users/{id}', 'AuthController@updateUser');
    $router->delete('/users/{id}', 'AuthController@deactivateUser');
    $router->patch('/users/{id}', 'AuthController@activateUser');

    $router->post('/users/login', 'AuthController@login');
    $router->post('/users/logout', 'AuthController@logout');

    $router->post('/users/refresh', 'AuthController@refreshToken');
 


    $router->get('/roles', 'RoleController@getAllRole');
    $router->get('/roles/{id}', 'RoleController@getRole');
    $router->post('/roles', 'RoleController@createRole');
    $router->put('/roles/{id}', 'RoleController@updateRole');
    $router->delete('/roles/{id}', 'RoleController@deactivateRole');
    $router->patch('/roles/{id}', 'RoleController@activateRole');

    $router->get('/external-user-types', 'ExternalUserTypeController@getAllExternalUserType');
    $router->get('/external-user-types/{id}', 'ExternalUserTypeController@getExternalUserType');
    $router->post('/external-user-types', 'ExternalUserTypeController@createExternalUserType');
    $router->put('/external-user-types/{id}', 'ExternalUserTypeController@updateExternalUserType');
    $router->delete('/external-user-types/{id}', 'ExternalUserTypeController@deactivateExternalUserType');
    $router->patch('/external-user-types/{id}', 'ExternalUserTypeController@activateExternalUserType');
});


