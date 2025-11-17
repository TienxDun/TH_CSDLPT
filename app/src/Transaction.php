<?php

/**
 * Thực hiện giao dịch mua hàng phân tán.
 * @param int $maKhachHang Mã khách hàng.
 * @param array $orders Danh sách các sản phẩm cần mua: [['MaSanPham' => 123, 'qty' => 2], ...]
 * @return array Kết quả giao dịch.
 */
function buyFromSites(int $maKhachHang, array $orders): array
{
  $connA = getPDOForSite('SITE_A');
  $connB = getPDOForSite('SITE_B');

  // 1. XÁC ĐỊNH SITE DỰA THEO KHÁCH HÀNG
  $khachHangSite = determineSite('KhachHang', $maKhachHang);
  $hoaDonConn = $khachHangSite === 'A' ? $connA : $connB;
  try {
    $maHoaDon = generateUniqueMaHoaDon($hoaDonConn, $khachHangSite);
  } catch (Exception $e) {
    return ['status' => 'error', 'message' => $e->getMessage()];
  }

  // Đảm bảo cả hai kết nối đều được bắt đầu để duy trì tính ACID
  try {
    $connA->beginTransaction();
    $connB->beginTransaction();

    // 2. TẠO HÓA ĐƠN GỐC (Chỉ trên site HoaDonConn)
    $stmt = $hoaDonConn->prepare("INSERT INTO HoaDon (MaHoaDon, MaKhachHang, Ngay) VALUES (?, ?, GETDATE())");
    $stmt->execute([$maHoaDon, $maKhachHang]);

    // 3. THỰC HIỆN TRỪ TỒN KHO VÀ TẠO CHI TIẾT HÓA ĐƠN
    foreach ($orders as $order) {
      $maSanPham = $order['MaSanPham'];
      $qty = $order['qty'];

      // Xác định site của Sản phẩm để trừ tồn kho
      $sanPhamSite = determineSite('SanPham', $maSanPham);
      $sanPhamConn = $sanPhamSite === 'A' ? $connA : $connB;

      // --- Thao tác 1: Trừ tồn kho (Có thể trên $connA hoặc $connB) ---
      $stmt = $sanPhamConn->prepare("UPDATE SanPham SET TonKho = TonKho - ? WHERE MaSanPham = ? AND TonKho >= ?");
      $stmt->execute([$qty, $maSanPham, $qty]);

      if ($stmt->rowCount() === 0) {
        // Nếu trừ tồn kho thất bại, ném Exception để kích hoạt Rollback toàn bộ
        throw new Exception("Không đủ hàng cho sản phẩm {$maSanPham} (site {$sanPhamSite})");
      }

      // --- Thao tác 2: Tạo Chi Tiết Hóa Đơn (Luôn trên $hoaDonConn) ---
      // ChiTietHoaDon phải đồng vị trí với HoaDon cha.
      $stmt = $hoaDonConn->prepare("INSERT INTO ChiTietHoaDon (MaHoaDon, MaSanPham, SoLuong) VALUES (?, ?, ?)");
      $stmt->execute([$maHoaDon, $maSanPham, $qty]);
    }

    // 4. COMMIT (Thực hiện Two-Phase Commit thủ công)
    // Lưu ý: Đây là điểm yếu của Giao dịch Phân tán thủ công.
    $connA->commit();
    $connB->commit();

    return ['status' => 'success', 'message' => "Tạo hóa đơn {$maHoaDon} thành công trên cả hai site"];

  } catch (Exception $e) {
    // 5. ROLLBACK TOÀN BỘ nếu có bất kỳ lỗi nào xảy ra
    if ($connA->inTransaction()) $connA->rollBack();
    if ($connB->inTransaction()) $connB->rollBack();

    // Thêm log chi tiết site Hóa đơn và Site Sản phẩm lỗi
    $errorMsg = "Rollback toàn bộ (Hóa đơn tạo tại Site: {$khachHangSite}): " . $e->getMessage();
    return ['status' => 'error', 'message' => $errorMsg];
  }
}