<?php

use FastRoute\Dispatcher;
use function FastRoute\simpleDispatcher;

// Load routes
$routes = require __DIR__ . '/web.php';

// Create dispatcher
$dispatcher = simpleDispatcher($routes);

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string if exists
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

// Dispatch the request
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo "404 Not Found";
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo "405 Method Not Allowed";
        break;

    case Dispatcher::FOUND:
        [$controller, $method] = $routeInfo[1];
        $vars = $routeInfo[2];

        // Instantiate controller and call method
        (new $controller())->$method(...$vars);
        break;
    default: {
        http_response_code(403);
        echo "Unauthorized access";
        break;
    }
}
