<?php
// config/app.php
//
// Sets BASE_PATH for the application, so routing works in subdirectories or root.
// BASE_PATH is used throughout the app for generating URLs and routing.
//
// Example: If app is at /lcn, BASE_PATH = '/lcn'. If at root, BASE_PATH = ''.
//
// Usage: define('BASE_PATH', $basePath);

// This calculates the base path of the application.
// It will be '/lcn' when in a subdirectory, or empty '' when in the root.
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = rtrim($scriptName, '/');

define('BASE_PATH', $basePath); 