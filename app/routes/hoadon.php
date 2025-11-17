<?php
require_once '../common.php';

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    if (isset($segments[1])) {
      // GET /hoadon/{id}
      $stmt = $pdo->prepare("SELECT * FROM HoaDon WHERE MaHoaDon = ?");
      $stmt->execute([$segments[1]]);
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($data) {
        response($data);
      } else {
        responseError(404, 'Hóa đơn không tồn tại');
      }
    } else {
      // GET /hoadon - liệt kê tất cả
      $stmt = $pdo->query("SELECT h.*, k.TenKh FROM HoaDon h JOIN KhachHang k ON h.MaKhachHang = k.MaKhachHang");
      $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
      response($items);
    }
    break;

  case 'POST':
    // Thêm hóa đơn mới
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['MaHoaDon']) || !isset($input['MaKhachHang']) || !isset($input['Ngay'])) {
      responseError(400, 'Thiếu thông tin hóa đơn (MaHoaDon, MaKhachHang, Ngay)');
    }
    try {
      $stmt = $pdo->prepare("INSERT INTO HoaDon (MaHoaDon, MaKhachHang, Ngay) VALUES (?, ?, ?)");
      $stmt->execute([$input['MaHoaDon'], $input['MaKhachHang'], $input['Ngay']]);
      response(['message' => 'Hóa đơn đã được thêm'], 201);
    } catch (Exception $e) {
      responseError(500, 'Lỗi khi thêm hóa đơn: ' . $e->getMessage());
    }
    break;

  default:
    responseError(405, 'Phương thức không được hỗ trợ');
    break;
}