<?php
$routes = require __DIR__ . '/app/routes/web.php';

if (!is_array($routes)) {
    http_response_code(500);
    echo 'Invalid routes configuration';
    exit;
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if ($method === 'HEAD') {
    $method = 'GET';
}

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath && $basePath !== '/') {
    if (strpos($requestPath, $basePath) === 0) {
        $requestPath = substr($requestPath, strlen($basePath));
    }
}
if ($requestPath === '') {
    $requestPath = '/';
}

$methodRoutes = [];
if (isset($routes[$method]) && is_array($routes[$method])) {
    $methodRoutes = $routes[$method];
} elseif (isset($routes['ANY']) && is_array($routes['ANY'])) {
    $methodRoutes = $routes['ANY'];
} elseif (isset($routes[$requestPath])) {
    $methodRoutes = $routes;
}

if (!isset($methodRoutes[$requestPath])) {
    $allowedMethods = [];
    foreach ($routes as $routeMethod => $routeMap) {
        if (is_array($routeMap) && isset($routeMap[$requestPath])) {
            $allowedMethods[] = strtoupper($routeMethod);
        }
    }

    if (!empty($allowedMethods)) {
        header('Allow: ' . implode(', ', array_unique($allowedMethods)));
        http_response_code(405);
        echo '405 Method Not Allowed';
        exit;
    }

    http_response_code(404);
    echo '404 Not Found';
    exit;
}

require $methodRoutes[$requestPath];
