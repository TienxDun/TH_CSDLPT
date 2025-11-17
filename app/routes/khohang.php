<?php
require_once '../common.php';

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    // GET /khohang - danh sách kho
    $stmt = $pdo->query("SELECT * FROM KhoHang");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    response($items);
    break;

  default:
    responseError(405, 'Phương thức không được hỗ trợ');
    break;
}