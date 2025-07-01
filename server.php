<?php
/*
 * server.php - Static file/dev server router for LCN Management System
 *
 * - Used with PHP's built-in server for local development.
 * - Serves static files from /public if they exist.
 * - Otherwise, routes all requests to index.php (the front controller).
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Path to the public directory
$public_path = __DIR__ . '/public';

// The full path to the requested file
$requested_file = $public_path . $uri;

// Check if the file exists in the public folder and it's not a directory
if ($uri !== '/' && file_exists($requested_file) && is_file($requested_file)) {
    // Get the file extension
    $extension = strtolower(pathinfo($requested_file, PATHINFO_EXTENSION));

    // A simple map of common MIME types for web assets
    $mime_types = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
    ];

    // Set the content type header based on the file extension
    if (isset($mime_types[$extension])) {
        header("Content-Type: {$mime_types[$extension]}");
    } else {
        // Fallback for unknown types
        header("Content-Type: text/plain");
    }

    // Serve the file
    readfile($requested_file);
    exit;
}

// If it's not a static file, load the application's front controller.
require_once __DIR__ . '/index.php'; 