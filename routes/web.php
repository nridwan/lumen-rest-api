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

$router->group(['prefix' => '/guest', 'middleware' => ['json-response']], function () use ($router) {
    $router->group(['middleware' => ['must-guest']], function () use ($router) {
        $router->post('/logout', 'ApiAppController@logout');
    });
    $router->group(['middleware' => ['guest-refresh']], function () use ($router) {
        $router->post('/refresh', 'ApiAppController@refresh');
    });
    $router->post('/auth', 'ApiAppController@auth');
});

$router->group(['middleware' => ['json-response', 'auth']], function () use ($router) {
    $router->get('/user/profile', 'UserController@profile');
});

$router->group(['prefix' => '/user/auth', 'middleware' => ['json-response']], function () use ($router) {
    $router->group(['middleware' => ['auth']], function () use ($router) {
        $router->post('/logout', 'UserController@logout');
    });
    $router->group(['middleware' => ['auth-refresh']], function () use ($router) {
        $router->post('/refresh', 'UserController@refresh');
    });
    $router->group(['middleware' => ['guest']], function () use ($router) {
        $router->post('/login', 'UserController@login');
    });
});
