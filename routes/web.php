<?php

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
    return 'API teste - Contele :: ' . $router->app->version();
});

$router->group(['prefix' => 'api/v1', 'namespace' => 'Api'], function() use ($router) {

    $router->get('/', function () use ($router) {
        return [
            'GET' => [
                'url' => 
                [
                    '/',
                    '/api/v1',
                    '/api/v1/{partida}/{destino}/{consumo}/{valorCombustivel}/'
                    
                ]
            ]
        ];
    });

    $router->get('/routes', [
        'as' => 'routes.get-routes',
        'uses' => 'RouteController@getRoutes'
        ]);

    $router->get('/{routeA}/{routeB}/{cons: \d*(?:\:\d+)?}/{gasPrice: \d*(?:\:\d+)?}/', [
        'as' => 'routes.best-route',
        'uses' => 'RouteController@bestRoute'
    ]);
});
