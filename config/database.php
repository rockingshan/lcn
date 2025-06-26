<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $e) {
    // Suppress the error if .env file is not found
    // In a production environment, you might want to handle this differently
}


$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_user = $_ENV['DB_USERNAME'] ?? 'root';
$db_pass = $_ENV['DB_PASSWORD'] ?? '';
$db_name = $_ENV['DB_DATABASE'] ?? '';

//$con = mysqli_connect($db_host, $db_user, $db_pass);
$auth = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$con || !$auth) {
    // In a real app, you'd want to log this error, not just die
    die("Database connection failed: " . mysqli_connect_error());
} 