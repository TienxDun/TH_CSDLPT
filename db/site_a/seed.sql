USE Shop;
GO
DELETE FROM ChiTietHoaDon;
DELETE FROM HoaDon;
DELETE FROM KhachHang;
DELETE FROM SanPham;
DELETE FROM KhoHang;
GO

-- Tạm tắt kiểm tra FK
ALTER TABLE HoaDon NOCHECK CONSTRAINT ALL;
ALTER TABLE ChiTietHoaDon NOCHECK CONSTRAINT ALL;
GO

-- ========== SITE A ==========
-- Kho
INSERT INTO KhoHang (MaKhoHang, TenKhoHang, DiaChi)
VALUES (1, N'Kho Hà Nội', N'123 Trần Duy Hưng');

-- SanPham 10001–60000 (50k)
INSERT INTO SanPham (MaSanPham, TenSanPham, GiaBan, MaKhoHang)
SELECT TOP (50000)
  ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) + 10000,
  CONCAT(N'SP_A_', ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) % 19, N'_',
         CHOOSE((ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) % 19)+1,
                N'Bút bi',N'Bút chì',N'Vở kẻ ngang',N'Laptop Dell',N'Laptop HP',
                N'Điện thoại iPhone',N'Điện thoại Samsung',N'Tai nghe Bluetooth',
                N'Bàn phím cơ',N'Chuột không dây',N'USB 32GB',N'Ổ cứng SSD',
                N'Màn hình 24inch',N'Máy in Canon',N'Ghế gaming',N'Balo laptop',
                N'Loa Bluetooth',N'Máy ảnh Sony',N'Smartwatch',N'Sách lập trình')),
  ABS(CHECKSUM(NEWID())) % 200000 + 1000,
  1
FROM sys.all_objects a CROSS JOIN sys.all_objects b;

-- Khách hàng 10001–15000 (5k)
INSERT INTO KhachHang (MaKhachHang, TenKH, DiaChi, SoDienThoai)
SELECT TOP (5000)
  ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) + 10000,
  CONCAT(N'KH_A_', ROW_NUMBER() OVER (ORDER BY (SELECT NULL))),
  N'Hà Nội',
  RIGHT('09' + CAST(ABS(CHECKSUM(NEWID())) % 1000000000 AS VARCHAR(10)),10)
FROM sys.all_objects a CROSS JOIN sys.all_objects b;

-- Hoá đơn 10001–110000 (100k)
INSERT INTO HoaDon (MaHoaDon, MaKhachHang, Ngay)
SELECT TOP (100000)
  ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) + 10000,
  kh.MaKhachHang,
  DATEADD(DAY, -ABS(CHECKSUM(NEWID())) % 365, SYSDATETIME())
FROM KhachHang kh
  CROSS JOIN sys.all_objects a CROSS JOIN sys.all_objects b;

-- Bật lại constraint
ALTER TABLE HoaDon CHECK CONSTRAINT ALL;
ALTER TABLE ChiTietHoaDon CHECK CONSTRAINT ALL;
GO

SELECT
  (SELECT COUNT(*) FROM KhoHang) AS Kho,
  (SELECT COUNT(*) FROM SanPham) AS SP,
  (SELECT COUNT(*) FROM KhachHang) AS KH,
  (SELECT COUNT(*) FROM HoaDon) AS HD,
  (SELECT COUNT(*) FROM ChiTietHoaDon) AS CTHD;