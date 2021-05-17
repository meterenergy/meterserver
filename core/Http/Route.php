<?php

class Route
{
    private static $routes = Array();
    
    public static function get(String $path, Closure $callback)
    {
        if (!array_key_exists($path, self::$routes)) {
            self::add('GET', $path, $callback);
        }
    }

    public static function post(String $path, Closure $callback)
    {
        if (!array_key_exists($path, self::$routes)) {
            self::add('POST', $path, $callback);
        }
    }

    private static function add(String $method, String $path, Closure $callback)
    {
        $route = [
            'CALLBACK' => $callback,
            'REQUEST_METHOD' => $method
        ];
        self::dispatch($path, $route);
    }

    private static function dispatch(String $path, Array $route)
    {
        if ($_SERVER['REQUEST_URI'] == BASE . $path && $_SERVER['REQUEST_METHOD'] == $route['REQUEST_METHOD']) {
            return call_user_func($route['CALLBACK']);
        }
        elseif ($_SERVER['REQUEST_URI'] == BASE . $path && $_SERVER['REQUEST_METHOD'] != $route['REQUEST_METHOD']) {
            echo "Invalid Request::" . $_SERVER['REQUEST_METHOD'];
        }
    }
}
