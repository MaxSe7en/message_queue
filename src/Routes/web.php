<?php
use FastRoute\RouteCollector;


return function (RouteCollector $router) {
    $router->addGroup('/api/v1', function (RouteCollector $router){

        $router->addRoute('GET', '/home', ['App\Controllers\HomeController', 'index']);
        $router->addRoute('GET', '/user/{id:\d+}', ['App\Controllers\UserController', 'show']);
        $router->addRoute('GET', '/add_momo_transaction', ['App\Controllers\MomoMsgController', 'viewMessage']);
        $router->post('/add_momo_transaction', 'App\Controllers\MomoMsgController::addMessage');
    });
    // $router->
};
