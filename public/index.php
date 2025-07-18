<?php
require __DIR__.'/../vendor/autoload.php';

use PocketPHP\Core\Router;

// Load helper functions
require __DIR__.'/../src/helpers.php';

// Load environment variables (already loaded in helpers.php)

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
    $router = new Router();
    require __DIR__.'/../config/routes.php'; // Load route definitions
    $router->dispatch();
} catch (Throwable $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
    exit;
}