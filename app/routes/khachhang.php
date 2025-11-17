<?php
require_once '../common.php';

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    if (isset($segments[1])) {
      // GET /khachhang/{id}
      $stmt = $pdo->prepare("SELECT * FROM KhachHang WHERE MaKhachHang = ?");
      $stmt->execute([$segments[1]]);
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($data) {
        response($data);
      } else {
        responseError(404, 'Khách hàng không tồn tại');
      }
    } else {
      // GET /khachhang
      $stmt = $pdo->query("SELECT * FROM KhachHang");
      $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
      response($items);
    }
    break;

  case 'POST':
    // Thêm khách hàng mới
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['MaKhachHang']) || !isset($input['TenKh']) || !isset($input['DiaChi']) || !isset($input['SoDienThoai'])) {
      responseError(400, 'Thiếu thông tin khách hàng (MaKhachHang, TenKh, DiaChi, SoDienThoai)');
    }
    try {
      $stmt = $pdo->prepare("INSERT INTO KhachHang (MaKhachHang, TenKh, DiaChi, SoDienThoai) VALUES (?, ?, ?, ?)");
      $stmt->execute([$input['MaKhachHang'], $input['TenKh'], $input['DiaChi'], $input['SoDienThoai']]);
      response(['message' => 'Khách hàng đã được thêm'], 201);
    } catch (Exception $e) {
      responseError(500, 'Lỗi khi thêm khách hàng: ' . $e->getMessage());
    }
    break;

  case 'PUT':
    // Cập nhật khách hàng
    if (!isset($segments[1])) {
      responseError(400, 'Thiếu ID khách hàng');
    }
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
      responseError(400, 'Dữ liệu không hợp lệ');
    }
    $updates = [];
    $params = [];
    if (isset($input['TenKh'])) {
      $updates[] = 'TenKh = ?';
      $params[] = $input['TenKh'];
    }
    if (isset($input['DiaChi'])) {
      $updates[] = 'DiaChi = ?';
      $params[] = $input['DiaChi'];
    }
    if (isset($input['SoDienThoai'])) {
      $updates[] = 'SoDienThoai = ?';
      $params[] = $input['SoDienThoai'];
    }
    if (empty($updates)) {
      responseError(400, 'Không có trường nào để cập nhật');
    }
    $params[] = $segments[1];
    try {
      $stmt = $pdo->prepare("UPDATE KhachHang SET " . implode(', ', $updates) . " WHERE MaKhachHang = ?");
      $stmt->execute($params);
      if ($stmt->rowCount() > 0) {
        response(['message' => 'Khách hàng đã được cập nhật']);
      } else {
        responseError(404, 'Khách hàng không tồn tại');
      }
    } catch (Exception $e) {
      responseError(500, 'Lỗi khi cập nhật khách hàng: ' . $e->getMessage());
    }
    break;

  case 'DELETE':
    // Xóa khách hàng
    if (!isset($segments[1])) {
      responseError(400, 'Thiếu ID khách hàng');
    }
    try {
      $stmt = $pdo->prepare("DELETE FROM KhachHang WHERE MaKhachHang = ?");
      $stmt->execute([$segments[1]]);
      if ($stmt->rowCount() > 0) {
        response(['message' => 'Khách hàng đã được xóa']);
      } else {
        responseError(404, 'Khách hàng không tồn tại');
      }
    } catch (Exception $e) {
      responseError(500, 'Lỗi khi xóa khách hàng: ' . $e->getMessage());
    }
    break;

  default:
    responseError(405, 'Phương thức không được hỗ trợ');
    break;
}