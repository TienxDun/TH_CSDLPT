<?php 
header('Content-Type: application/json; charset=utf-8'); 
 
$dsn = "sqlsrv:Server={$_ENV['DB_HOST']},{$_ENV['DB_PORT']};"
         . "Database={$_ENV['DB_NAME']};TrustServerCertificate=1";
 
try { 
    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [ 
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION 
    ]); 
} catch (Exception $e) { 
    http_response_code(500); 
    echo json_encode([
      'error' => 'DB connection failed',
      'detail' => $e->getMessage(),
    ]); 
    exit; 
} 
 
$path = $_SERVER['REQUEST_URI']; 
 
if ($path === '/sanpham') { 
    $stmt = $pdo->query("SELECT * FROM SANPHAM"); 
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE); 
    exit; 
} 
 
if ($path === '/khachhang') { 
    $stmt = $pdo->query("SELECT * FROM KHACHHANG"); 
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE); 
    exit; 
} 
 
http_response_code(404); 
echo json_encode(['error'=>'Endpoint not found']); 