<?php
require_once '../common.php';

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    if (isset($segments[1])) {
      // GET /chitiethoadon/{mahoadon} - xem chi tiết theo hóa đơn
      $stmt = $pdo->prepare("SELECT c.*, s.TenSanPham, s.GiaBan FROM ChiTietHoaDon c JOIN SanPham s ON c.MaSanPham = s.MaSanPham WHERE c.MaHoaDon = ?");
      $stmt->execute([$segments[1]]);
      $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
      response($items);
    } else {
      responseError(400, 'Thiếu MaHoaDon');
    }
    break;

  case 'POST':
    // Thêm dòng sản phẩm vào chi tiết hóa đơn
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['MaHoaDon']) || !isset($input['MaSanPham']) || !isset($input['SoLuong'])) {
      responseError(400, 'Thiếu thông tin chi tiết (MaHoaDon, MaSanPham, SoLuong)');
    }
    try {
      $stmt = $pdo->prepare("INSERT INTO ChiTietHoaDon (MaHoaDon, MaSanPham, SoLuong) VALUES (?, ?, ?)");
      $stmt->execute([$input['MaHoaDon'], $input['MaSanPham'], $input['SoLuong']]);
      response(['message' => 'Chi tiết hóa đơn đã được thêm'], 201);
    } catch (Exception $e) {
      responseError(500, 'Lỗi khi thêm chi tiết: ' . $e->getMessage());
    }
    break;

  default:
    responseError(405, 'Phương thức không được hỗ trợ');
    break;
}