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
      $stmt = $pdo->query("SELECT s.*, k.TenKhoHang FROM SanPham s JOIN KhoHang k ON s.MaKhoHang = k.MaKhoHang");
      $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
      response($items);
    }
    break;

  case 'POST':
    // Thêm sản phẩm mới
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['MaSanPham']) || !isset($input['TenSanPham']) || !isset($input['Gia']) || !isset($input['MaKhoHang'])) {
      responseError(400, 'Thiếu thông tin sản phẩm (MaSanPham, TenSanPham, Gia, MaKhoHang)');
    }
    try {
      $stmt = $pdo->prepare("INSERT INTO SanPham (MaSanPham, TenSanPham, GiaBan, MaKhoHang) VALUES (?, ?, ?, ?)");
      $stmt->execute([$input['MaSanPham'], $input['TenSanPham'], $input['Gia'], $input['MaKhoHang']]);
      response(['message' => 'Sản phẩm đã được thêm'], 201);
    } catch (Exception $e) {
      responseError(500, 'Lỗi khi thêm sản phẩm: ' . $e->getMessage());
    }
    break;

  case 'PUT':
    // Cập nhật sản phẩm
    if (!isset($segments[1])) {
      responseError(400, 'Thiếu ID sản phẩm');
    }
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
      responseError(400, 'Dữ liệu không hợp lệ');
    }
    $updates = [];
    $params = [];
    if (isset($input['TenSanPham'])) {
      $updates[] = 'TenSanPham = ?';
      $params[] = $input['TenSanPham'];
    }
    if (isset($input['Gia'])) {
      $updates[] = 'GiaBan = ?';
      $params[] = $input['Gia'];
    }
    if (isset($input['MaKhoHang'])) {
      $updates[] = 'MaKhoHang = ?';
      $params[] = $input['MaKhoHang'];
    }
    if (empty($updates)) {
      responseError(400, 'Không có trường nào để cập nhật');
    }
    $params[] = $segments[1];
    try {
      $stmt = $pdo->prepare("UPDATE SanPham SET " . implode(', ', $updates) . " WHERE MaSanPham = ?");
      $stmt->execute($params);
      if ($stmt->rowCount() > 0) {
        response(['message' => 'Sản phẩm đã được cập nhật']);
      } else {
        responseError(404, 'Sản phẩm không tồn tại');
      }
    } catch (Exception $e) {
      responseError(500, 'Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
    }
    break;

  case 'DELETE':
    // Xóa sản phẩm
    if (!isset($segments[1])) {
      responseError(400, 'Thiếu ID sản phẩm');
    }
    try {
      $stmt = $pdo->prepare("DELETE FROM SanPham WHERE MaSanPham = ?");
      $stmt->execute([$segments[1]]);
      if ($stmt->rowCount() > 0) {
        response(['message' => 'Sản phẩm đã được xóa']);
      } else {
        responseError(404, 'Sản phẩm không tồn tại');
      }
    } catch (Exception $e) {
      responseError(500, 'Lỗi khi xóa sản phẩm: ' . $e->getMessage());
    }
    break;

  default:
    responseError(405, 'Phương thức không được hỗ trợ');
    break;
}