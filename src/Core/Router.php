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
        
        // Handle method spoofing for PUT and DELETE requests
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        // Check for static routes first
        if (isset($this->routes[$method][$uri])) {
            return $this->callHandler($this->routes[$method][$uri]);
        }
        
        // Check for dynamic routes
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $handler) {
                // Convert route to regex pattern
                $pattern = preg_replace('/\//', '\/', $route);
                $pattern = preg_replace('/\{([^\/]+)\}/', '(?P<\1>[^\/]+)', $pattern);
                $pattern = "/^" . $pattern . "$/i";
                
                if (preg_match($pattern, $uri, $matches)) {
                    // Remove full match from matches
                    array_shift($matches);
                    
                    // Get named parameters
                    $params = [];
                    foreach ($matches as $key => $value) {
                        if (!is_numeric($key)) {
                            $params[$key] = $value;
                        }
                    }
                    
                    return $this->callHandler($handler, $params);
                }
            }
        }
        
        // No route found - show 404
        $this->show404();
    }
    
    /**
     * Display the 404 error page
     */
    protected function show404()
    {
        http_response_code(404);
        $errorFile = __DIR__ . '/../../templates/errors/404.php';
        
        if (file_exists($errorFile)) {
            include $errorFile;
        } else {
            // Fallback error message if 404 template doesn't exist
            header('Content-Type: text/html');
            echo '<h1>404 Not Found</h1>';
            echo '<p>The requested URL was not found on this server.</p>';
        }
        exit;
    }
    
    protected function callHandler($handler, $params = [])
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
        
        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$class, $method] = explode('@', $handler);
            $controller = new $class();
            
            // If we have parameters, pass them to the method
            if (!empty($params)) {
                return call_user_func_array([$controller, $method], $params);
            }
            
            return $controller->$method();
        }
        
        throw new \Exception('Invalid route handler');
    }
}