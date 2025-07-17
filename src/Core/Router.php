<?php
namespace PocketPHP\Core;

class Router
{
    protected $routes = [];
    
    public function addRoute($method, $uri, $handler)
    {
        $this->routes[$method][$uri] = $handler;
    }
    
    public function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            
            if (is_callable($handler)) {
                return $handler();
            }
            
            if (is_string($handler) && strpos($handler, '@') !== false) {
                [$class, $method] = explode('@', $handler);
                $controller = new $class();
                return $controller->$method();
            }
        }
        
        http_response_code(404);
        echo '404 Not Found';
    }
}