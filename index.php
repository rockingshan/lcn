<?php
// index.php - Front Controller
session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/app.php'; // Defines BASE_PATH
require_once __DIR__ . '/config/database.php'; // Provides $auth connection

use App\Controllers\AuthController;
use App\Controllers\HomeController;

// --- Authentication Middleware ---
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// If user is not logged in and not trying to access login page, redirect them.
if (!isset($_SESSION['user_id']) && $request_uri !== BASE_PATH . '/login') {
    header('Location: ' . BASE_PATH . '/login');
    exit();
}
// If user IS logged in and tries to access login page, redirect to home.
if (isset($_SESSION['user_id']) && $request_uri === BASE_PATH . '/login') {
    header('Location: ' . BASE_PATH . '/');
    exit();
}
// --- End of Middleware ---


$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($auth) {
    
    // Auth Routes
    $r->addRoute('GET', '/login', [new AuthController($auth), 'showLoginForm']);
    $r->addRoute('POST', '/login', [new AuthController($auth), 'login']);
    $r->addRoute('GET', '/logout', [new AuthController($auth), 'logout']);

    // City change route
    $r->addRoute('POST', '/set-city', [new HomeController($auth), 'setCity']);

    // Route for the homepage (now protected and using a controller)
    $r->addRoute('GET', '/', [new HomeController($auth), 'index']);

    // Edit Channel routes
    $r->addRoute('GET', '/edit-channel/{channel_id:\d+}', [new HomeController($auth), 'editChannelForm']);
    $r->addRoute('POST', '/edit-channel/{channel_id:\d+}', [new HomeController($auth), 'editChannelSubmit']);

    // Modify LCN routes
    $r->addRoute('GET', '/modify-lcn/{cmap_id:\d+}', [new HomeController($auth), 'modifyLcnForm']);
    $r->addRoute('POST', '/modify-lcn/{cmap_id:\d+}', [new HomeController($auth), 'modifyLcnSubmit']);

    // Swap LCN routes
    $r->addRoute('GET', '/swap-lcn/{cmap_id:\d+}', [new HomeController($auth), 'swapLcnForm']);
    $r->addRoute('POST', '/swap-lcn/{cmap_id:\d+}', [new HomeController($auth), 'swapLcnSubmit']);

    // Logs page
    $r->addRoute('GET', '/logs', [new HomeController($auth), 'logsPage']);

    // IRD Inventory page
    $r->addRoute('GET', '/ird-inventory', [new HomeController($auth), 'irdInventoryPage']);

    // IRD Inventory CRUD
    $r->addRoute('GET', '/ird-inventory/add', [new HomeController($auth), 'irdAddForm']);
    $r->addRoute('POST', '/ird-inventory/add', [new HomeController($auth), 'irdAddSubmit']);
    $r->addRoute('GET', '/ird-inventory/edit/{ird_id:\d+}', [new HomeController($auth), 'irdEditForm']);
    $r->addRoute('POST', '/ird-inventory/edit/{ird_id:\d+}', [new HomeController($auth), 'irdEditSubmit']);
    $r->addRoute('POST', '/ird-inventory/delete/{ird_id:\d+}', [new HomeController($auth), 'irdDelete']);

    // Add SID and Add Channel
    $r->addRoute('GET', '/add-sid', [new HomeController($auth), 'addSidForm']);
    $r->addRoute('POST', '/add-sid', [new HomeController($auth), 'addSidSubmit']);
    $r->addRoute('GET', '/add-channel', [new HomeController($auth), 'addChannelForm']);
    $r->addRoute('POST', '/add-channel', [new HomeController($auth), 'addChannelSubmit']);

    // AJAX: Check SID uniqueness
    $r->addRoute('POST', '/ajax/check-sid', [new HomeController($auth), 'ajaxCheckSid']);

    // Add Channel Mapping
    $r->addRoute('GET', '/add-channel-mapping', [new HomeController($auth), 'addChannelMappingForm']);
    $r->addRoute('POST', '/add-channel-mapping', [new HomeController($auth), 'addChannelMappingSubmit']);

    // Export LCN Excel
    $r->addRoute('GET', '/export-lcn', [new HomeController($auth), 'exportLcnExcel']);

    // IRD Challan Details
    $r->addRoute('GET', '/ird-challan', [new HomeController($auth), 'irdChallanList']);
    $r->addRoute('GET', '/ird-challan/add', [new HomeController($auth), 'irdChallanAddForm']);
    $r->addRoute('POST', '/ird-challan/add', [new HomeController($auth), 'irdChallanAddSubmit']);
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// This part makes the router work correctly in subdirectories
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/' && $basePath !== '\\' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}

// If the URI is empty after stripping the base path, it means we are at the root.
if (empty($uri)) {
    $uri = '/';
}

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        http_response_code(404);
        require 'custom_404.html';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        http_response_code(405);
        echo '405 - Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // Call the handler
        call_user_func_array($handler, $vars);
        break;
} 