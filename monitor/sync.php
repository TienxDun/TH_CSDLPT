<?php
$site = $argv[1] ?? null;
if (!$site) die("Missing site param\n");

$dsn = "sqlsrv:Server=mssql_global_123456;Database=Shop;TrustServerCertificate=1";
$user = "sa";
$pass = "Your@STROng!Pass#Word";
$global = 'mssql_global_123456';
try {
  $pdo = new PDO($dsn, $user, $pass);
  $stmt = $pdo->query("EXEC SyncData_$site");
  echo "Đồng bộ site $site hoàn tất\n";
} catch (Exception $e) {
  echo "Đồng bộ site $site lỗi: " . $e->getMessage() . "\n";
  exit(1);
}