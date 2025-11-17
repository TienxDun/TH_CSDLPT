<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require __DIR__ . '/../common.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', $path);
$prefix = preg_replace('/[^a-zA-Z0-9_-]/', '', $segments[0] ?: 'default');

$routeFile = __DIR__ . "/../routes/{$prefix}.php";

if (file_exists($routeFile)) {
    require $routeFile;
} else {
    response(null, 404, 'Not Found');
}