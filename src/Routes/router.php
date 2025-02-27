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
// print_r($routeInfo);
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
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        if (is_array($handler)) {// for this types of routes ['App\Controllers\UserController', 'show']
            [$controller, $method] = $handler;
            (new $controller())->$method(...array_values($vars));
        } elseif (is_string($handler) && strpos($handler, '::') !== false) { // for this types of routes 'App\Controllers\MomoMsgController::addMessage'
            [$controller, $method] = explode('::', $handler);
            (new $controller())->$method(...array_values($vars));
        }
        break;
    default: {
        http_response_code(403);
        echo "Unauthorized access";
        break;
    }
}
