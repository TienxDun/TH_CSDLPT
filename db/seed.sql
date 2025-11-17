USE Shop;

INSERT INTO dbo.KhoHang (MaKhoHang, TenKhoHang, DiaChi) VALUES
  (1, N'Kho Hà Nội', N'123 Trần Duy Hưng'),
  (2, N'Kho HCM', N'456 Nguyễn Huệ');

INSERT INTO dbo.SanPham (MaSanPham, TenSanPham, GiaBan, MaKhoHang) VALUES
  (101, N'Bút bi', 5000, 1),
  (102, N'Vở kẻ ngang', 10000, 1),
  (201, N'Laptop Dell', 15000000, 2);

INSERT INTO dbo.KhachHang (MaKhachHang, TenKh, DiaChi, SoDienThoai) VALUES
  (1, N'Nguyễn Văn A', N'Hà Nội', '0911111111'),
  (2, N'Trần Thị B', N'HCM', '0922222222');

INSERT INTO dbo.HoaDon (MaHoaDon, MaKhachHang, Ngay) VALUES
  (1001, 1, SYSDATETIME()),
  (1002, 2, SYSDATETIME());

INSERT INTO dbo.ChiTietHoaDon (MaHoaDon, MaSanPham, SoLuong) VALUES
  (1001, 101, 10),
  (1001, 102, 5),
  (1002, 201, 1);