USE Shop;
GO
DELETE FROM ChiTietHoaDon;
DELETE FROM HoaDon;
DELETE FROM KhachHang;
DELETE FROM SanPham;
DELETE FROM KhoHang;
GO

ALTER TABLE HoaDon NOCHECK CONSTRAINT ALL;
ALTER TABLE ChiTietHoaDon NOCHECK CONSTRAINT ALL;
GO

-- ========== SITE B ==========
-- Kho
INSERT INTO KhoHang (MaKhoHang, TenKhoHang, DiaChi)
VALUES (2, N'Kho HCM', N'456 Nguyễn Huệ');

-- SanPham 60001–110000 (50k)
INSERT INTO SanPham (MaSanPham, TenSanPham, GiaBan, MaKhoHang)
SELECT TOP (50000)
  ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) + 60000,
  CONCAT(N'SP_B_', ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) % 19, N'_',
         CHOOSE((ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) % 19)+1,
                N'Bút bi',N'Bút chì',N'Vở kẻ ngang',N'Laptop Dell',N'Laptop HP',
                N'Điện thoại iPhone',N'Điện thoại Samsung',N'Tai nghe Bluetooth',
                N'Bàn phím cơ',N'Chuột không dây',N'USB 32GB',N'Ổ cứng SSD',
                N'Màn hình 24inch',N'Máy in Canon',N'Ghế gaming',N'Balo laptop',
                N'Loa Bluetooth',N'Máy ảnh Sony',N'Smartwatch',N'Sách lập trình')),
  ABS(CHECKSUM(NEWID())) % 200000 + 5000,
  2
FROM sys.all_objects a CROSS JOIN sys.all_objects b;

-- Khách hàng 15001–20000 (5k)
INSERT INTO KhachHang (MaKhachHang, TenKH, DiaChi, SoDienThoai)
SELECT TOP (5000)
  ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) + 15000,
  CONCAT(N'KH_B_', ROW_NUMBER() OVER (ORDER BY (SELECT NULL))),
  N'HCM',
  RIGHT('09' + CAST(ABS(CHECKSUM(NEWID())) % 1000000000 AS VARCHAR(10)),10)
FROM sys.all_objects a CROSS JOIN sys.all_objects b;

-- Hoá đơn 110001–210000 (100k)
INSERT INTO HoaDon (MaHoaDon, MaKhachHang, Ngay)
SELECT TOP (100000)
  ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) + 110000,
  kh.MaKhachHang,
  DATEADD(DAY, -ABS(CHECKSUM(NEWID())) % 365, SYSDATETIME())
FROM KhachHang kh
  CROSS JOIN sys.all_objects a CROSS JOIN sys.all_objects b;
GO

SELECT
  (SELECT COUNT(*) FROM KhoHang) AS Kho,
  (SELECT COUNT(*) FROM SanPham) AS SP,
  (SELECT COUNT(*) FROM KhachHang) AS KH,
  (SELECT COUNT(*) FROM HoaDon) AS HD,
  (SELECT COUNT(*) FROM ChiTietHoaDon) AS CTHD;