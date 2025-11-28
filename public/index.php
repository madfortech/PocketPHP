<?php

session_start();

require __DIR__.'/../vendor/autoload.php';
// Set the log path to your project's storage directory
use PocketErrorLog\ErrorLog;
ErrorLog::setLogPath(__DIR__ . '/../storage/logs/app.log');

use PocketPHP\Core\Router;
// Load helper functions
require __DIR__.'/../src/helpers.php';

// Load timezone configuration
$timezoneConfig = require __DIR__.'/../config/timezone.php';
date_default_timezone_set($timezoneConfig['timezone']);

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

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