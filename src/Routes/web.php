<?php
use FastRoute\RouteCollector;

return function (RouteCollector $router) {
    $router->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);
    $router->addRoute('GET', '/user/{id:\d+}', ['App\Controllers\UserController', 'show']);
    // $router->
};
