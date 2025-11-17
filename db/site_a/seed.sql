USE Shop;
GO

-- Kho hàng tại site A
INSERT INTO KhoHang (MaKhoHang, TenKhoHang, DiaChi) VALUES
(1, N'Kho Hà Nội', N'123 Trần Duy Hưng');

-- Sản phẩm tại site A
INSERT INTO SanPham (MaSanPham, TenSanPham, GiaBan, MaKhoHang) VALUES
(101, N'Bút bi', 5000, 1),
(102, N'Vở kẻ ngang', 10000, 1);

-- Khách hàng tại site A
INSERT INTO KhachHang (MaKhachHang, TenKH, DiaChi, SoDienThoai) VALUES
(1, N'Nguyễn Văn A', N'Hà Nội', '0911111111');

-- Hóa đơn tại site A
INSERT INTO HoaDon (MaHoaDon, MaKhachHang, Ngay) VALUES
(1001, 1, SYSDATETIME());

-- Chi tiết hóa đơn tại site A
INSERT INTO ChiTietHoaDon (MaHoaDon, MaSanPham, SoLuong) VALUES
(1001, 101, 10),
(1001, 102, 5);