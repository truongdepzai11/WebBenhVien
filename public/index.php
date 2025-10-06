<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Clear OPcache (for development)
if (function_exists('opcache_reset')) {
    opcache_reset();
}

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Load helpers
require_once __DIR__ . '/../app/Helpers/Auth.php';
require_once __DIR__ . '/../app/Helpers/Validator.php';

// Load routes
$router = require_once __DIR__ . '/../routes/web.php';

// Dispatch request
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($method, $uri);
