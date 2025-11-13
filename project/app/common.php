<?php 
header('Content-Type: application/json; charset=utf-8'); 
$dsn = "sqlsrv:Server=" . $_ENV['DB_HOST'] . "," . $_ENV['DB_PORT']
       . ";Database=" . $_ENV['DB_NAME'] . ";TrustServerCertificate=1";
global $pdo;

function response($data = null, $code = 200, $error = null)
{
  http_response_code($code);
  $data ??= [];
  if ($error) $data['error'] = $error;
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function responseError($code, $error)
{
  response(null, $code, $error);
}

try {
  $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8
  ]);
} catch (Exception $e) {
  responseError(500, 'DB connection failed');
}