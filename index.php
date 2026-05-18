<?php
/**
 * Front Controller – Zimnat PRS
 * All requests are routed through this file.
 */

declare(strict_types=1);

define('ROOT', __DIR__);

require_once ROOT . '/config/config.php';
require_once ROOT . '/config/Database.php';

// ── Auto-loader ───────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $dirs = [
        ROOT . '/models/',
        ROOT . '/controllers/',
        ROOT . '/services/',
        ROOT . '/middleware/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ── Session ───────────────────────────────────────────────────
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

// ── CSRF Token ────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Routing ───────────────────────────────────────────────────
$request = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Strip base path prefix when deployed in a sub-directory
$base    = trim(BASE_URL, '/');
if ($base !== '' && str_starts_with($request, $base)) {
    $request = trim(substr($request, strlen($base)), '/');
}

$segments   = explode('/', $request);
$controller = $segments[0] ?: 'dashboard';
$action     = $segments[1] ?? 'index';
$param      = $segments[2] ?? null;

// ── Dispatch ──────────────────────────────────────────────────
$routes = [
    'auth'       => 'AuthController',
    'dashboard'  => 'DashboardController',
    'policies'   => 'PolicyController',
    'documents'  => 'DocumentController',
    'users'      => 'UserController',
];

// Redirect root to dashboard
if ($controller === '' || $controller === 'index.php') {
    $controller = 'dashboard';
}

$controllerClass = $routes[$controller] ?? null;

if ($controllerClass === null) {
    http_response_code(404);
    include ROOT . '/views/partials/404.php';
    exit;
}

$ctrl = new $controllerClass();

if (!method_exists($ctrl, $action)) {
    http_response_code(404);
    include ROOT . '/views/partials/404.php';
    exit;
}

$ctrl->$action($param);
