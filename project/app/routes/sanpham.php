<?php
global $pdo;

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    if (isset($segments[1])) {
      // GET /sanpham/{id}
      $stmt = $pdo->prepare("SELECT * FROM SanPham WHERE MaSanPham = ?");
      $stmt->execute([$segments[1]]);
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($data) {
        response($data);
      } else {
        responseError(404, 'Sản phẩm không tồn tại');
      }
    } else {
      // GET /sanpham
      $stmt = $pdo->query("SELECT * FROM SanPham");
      $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
      response($items);
    }
    break;

  case 'POST':
    // TODO: thêm sản phẩm
    responseError(404, 'POST /sanpham chưa được triển khai');
    break;

  case 'PUT':
    // TODO: cập nhật sản phẩm
    responseError(404, 'PUT /sanpham chưa được triển khai');
    break;

  case 'DELETE':
    // TODO: xóa sản phẩm
    responseError(404, 'DELETE /sanpham chưa được triển khai');
    break;

  default:
    responseError(405, 'Phương thức không được hỗ trợ');
    break;
}