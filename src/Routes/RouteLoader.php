<?php

namespace App\Config;

use FastRoute\RouteCollector;

class RouteLoader
{
    public static function loadRoutes(): callable
    {
        return function (RouteCollector $r) {
            require_once __DIR__ . '/web.php';
        };
    }
}