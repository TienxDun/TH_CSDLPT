<?php
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