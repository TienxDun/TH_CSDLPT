<?php
global $pdo;

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    if (isset($segments[1])) {
      // GET /hoadon/{id} - Chi tiết hóa đơn
      $stmt = $pdo->prepare("
        SELECT hd.MaHoaDon, hd.Ngay, kh.TenKh, kh.DiaChi, kh.SoDienThoai,
               ct.MaSanPham, sp.TenSanPham, sp.GiaBan, ct.SoLuong
        FROM HoaDon hd
        JOIN KhachHang kh ON hd.MaKhachHang = kh.MaKhachHang
        LEFT JOIN ChiTietHoaDon ct ON hd.MaHoaDon = ct.MaHoaDon
        LEFT JOIN SanPham sp ON ct.MaSanPham = sp.MaSanPham
        WHERE hd.MaHoaDon = ?
        ORDER BY ct.MaSanPham
      ");
      $stmt->execute([$segments[1]]);
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if (empty($rows)) {
        responseError(404, 'Hóa đơn không tồn tại');
      }
      $hoaDon = [
        'MaHoaDon' => $rows[0]['MaHoaDon'],
        'Ngay' => $rows[0]['Ngay'],
        'KhachHang' => [
          'TenKh' => $rows[0]['TenKh'],
          'DiaChi' => $rows[0]['DiaChi'],
          'SoDienThoai' => $rows[0]['SoDienThoai']
        ],
        'ChiTiet' => []
      ];
      $tongTien = 0;
      foreach ($rows as $row) {
        if ($row['MaSanPham']) {
          $thanhTien = $row['GiaBan'] * $row['SoLuong'];
          $hoaDon['ChiTiet'][] = [
            'MaSanPham' => $row['MaSanPham'],
            'TenSanPham' => $row['TenSanPham'],
            'GiaBan' => $row['GiaBan'],
            'SoLuong' => $row['SoLuong'],
            'ThanhTien' => $thanhTien
          ];
          $tongTien += $thanhTien;
        }
      }
      $hoaDon['TongTien'] = $tongTien;
      response($hoaDon);
    } else {
      // GET /hoadon - Danh sách hóa đơn
      $stmt = $pdo->query("
        SELECT hd.MaHoaDon, hd.Ngay, kh.TenKh, kh.DiaChi, kh.SoDienThoai
        FROM HoaDon hd
        JOIN KhachHang kh ON hd.MaKhachHang = kh.MaKhachHang
        ORDER BY hd.Ngay DESC
      ");
      $hoaDons = $stmt->fetchAll(PDO::FETCH_ASSOC);
      response($hoaDons);
    }
    break;

  case 'POST':
    // TODO: tạo hóa đơn mới
    responseError(404, 'POST /hoadon chưa được triển khai');
    break;

  case 'PUT':
    // TODO: cập nhật hóa đơn
    responseError(404, 'PUT /hoadon chưa được triển khai');
    break;

  case 'DELETE':
    // TODO: xóa hóa đơn
    responseError(404, 'DELETE /hoadon chưa được triển khai');
    break;

  default:
    responseError(405, 'Phương thức không được hỗ trợ');
    break;
}