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
    // Thêm sản phẩm mới
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['MaSanPham'], $input['TenSanPham'], $input['GiaBan'], $input['MaKhoHang'])) {
      responseError(400, 'Dữ liệu không hợp lệ. Cần MaSanPham, TenSanPham, GiaBan, MaKhoHang');
    }
    // Kiểm tra MaKhoHang tồn tại
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM KhoHang WHERE MaKhoHang = ?");
    $stmt->execute([$input['MaKhoHang']]);
    if ($stmt->fetchColumn() == 0) {
      responseError(400, 'MaKhoHang không tồn tại');
    }
    // Kiểm tra MaSanPham chưa tồn tại
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM SanPham WHERE MaSanPham = ?");
    $stmt->execute([$input['MaSanPham']]);
    if ($stmt->fetchColumn() > 0) {
      responseError(400, 'MaSanPham đã tồn tại');
    }
    // Thêm sản phẩm
    $stmt = $pdo->prepare("INSERT INTO SanPham (MaSanPham, TenSanPham, GiaBan, MaKhoHang) VALUES (?, ?, ?, ?)");
    $stmt->execute([$input['MaSanPham'], $input['TenSanPham'], $input['GiaBan'], $input['MaKhoHang']]);
    response(['message' => 'Sản phẩm đã được thêm', 'MaSanPham' => $input['MaSanPham']], 201);
    break;

  case 'PUT':
    // Cập nhật sản phẩm
    if (!isset($segments[1])) {
      responseError(400, 'Cần MaSanPham trong URL');
    }
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
      responseError(400, 'Dữ liệu JSON không hợp lệ');
    }
    // Kiểm tra sản phẩm tồn tại
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM SanPham WHERE MaSanPham = ?");
    $stmt->execute([$segments[1]]);
    if ($stmt->fetchColumn() == 0) {
      responseError(404, 'Sản phẩm không tồn tại');
    }
    // Kiểm tra MaKhoHang nếu có
    if (isset($input['MaKhoHang'])) {
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM KhoHang WHERE MaKhoHang = ?");
      $stmt->execute([$input['MaKhoHang']]);
      if ($stmt->fetchColumn() == 0) {
        responseError(400, 'MaKhoHang không tồn tại');
      }
    }
    // Cập nhật
    $updateFields = [];
    $params = [];
    if (isset($input['TenSanPham'])) {
      $updateFields[] = 'TenSanPham = ?';
      $params[] = $input['TenSanPham'];
    }
    if (isset($input['GiaBan'])) {
      $updateFields[] = 'GiaBan = ?';
      $params[] = $input['GiaBan'];
    }
    if (isset($input['MaKhoHang'])) {
      $updateFields[] = 'MaKhoHang = ?';
      $params[] = $input['MaKhoHang'];
    }
    if (empty($updateFields)) {
      responseError(400, 'Không có trường nào để cập nhật');
    }
    $params[] = $segments[1];
    $stmt = $pdo->prepare("UPDATE SanPham SET " . implode(', ', $updateFields) . " WHERE MaSanPham = ?");
    $stmt->execute($params);
    response(['message' => 'Sản phẩm đã được cập nhật', 'MaSanPham' => $segments[1]]);
    break;

  case 'DELETE':
    // Xóa sản phẩm
    if (!isset($segments[1])) {
      responseError(400, 'Cần MaSanPham trong URL');
    }
    // Kiểm tra sản phẩm tồn tại
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM SanPham WHERE MaSanPham = ?");
    $stmt->execute([$segments[1]]);
    if ($stmt->fetchColumn() == 0) {
      responseError(404, 'Sản phẩm không tồn tại');
    }
    // Kiểm tra có trong ChiTietHoaDon không
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ChiTietHoaDon WHERE MaSanPham = ?");
    $stmt->execute([$segments[1]]);
    if ($stmt->fetchColumn() > 0) {
      responseError(400, 'Không thể xóa sản phẩm đã có trong hóa đơn');
    }
    // Xóa
    $stmt = $pdo->prepare("DELETE FROM SanPham WHERE MaSanPham = ?");
    $stmt->execute([$segments[1]]);
    response(['message' => 'Sản phẩm đã được xóa', 'MaSanPham' => $segments[1]]);
    break;

  default:
    responseError(405, 'Phương thức không được hỗ trợ');
    break;
}