<?php
/**
 * Sanskar AI - Application Entry Point
 * ======================================
 * All requests are routed through this file
 * For shared hosting deployment (WinSCP)
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Define base path (root directory)
define('BASE_PATH', __DIR__);

// Autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';

    // Check if class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get relative class name
    $relativeClass = substr($class, $len);

    // Convert namespace separators to directory separators
    $path = str_replace('\\', '/', $relativeClass);

    // Split into parts (directory and filename)
    $parts = explode('/', $path);
    $filename = array_pop($parts);

    // Convert directory names to lowercase, keep filename as-is
    $directory = implode('/', array_map('strtolower', $parts));

    // Build final path
    $file = $baseDir . ($directory ? $directory . '/' : '') . $filename . '.php';

    // Require file if it exists
    if (file_exists($file)) {
        require $file;
    }
});

// Load configuration
require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/config/app.php';

// Initialize application
\App\Config\App::init();

// Load routes
require_once BASE_PATH . '/app/routes.php';

// Dispatch router
\App\Core\Router::dispatch();
