<?php
require 'vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// Sample route definition
$dispatcher = simpleDispatcher(function (RouteCollector $router) {
    $router->addRoute('GET', '/test', function() {
        echo "FastRoute is working!";
    });
});

// Simulate a request
$httpMethod = 'GET';
$uri = '/test';

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $handler(); // Execute route handler
        break;
}
