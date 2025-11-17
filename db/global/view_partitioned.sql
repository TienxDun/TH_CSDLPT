USE Shop;
GO

IF OBJECT_ID('dbo.ChiTietHoaDon', 'V') IS NOT NULL DROP VIEW dbo.ChiTietHoaDon;
IF OBJECT_ID('dbo.HoaDon', 'V') IS NOT NULL DROP VIEW dbo.HoaDon;
IF OBJECT_ID('dbo.KhachHang', 'V') IS NOT NULL DROP VIEW dbo.KhachHang;
IF OBJECT_ID('dbo.SanPham', 'V') IS NOT NULL DROP VIEW dbo.SanPham;
IF OBJECT_ID('dbo.KhoHang', 'V') IS NOT NULL DROP VIEW dbo.KhoHang;
GO

-- KhoHang
CREATE VIEW dbo.KhoHang AS
SELECT * FROM mssql_site_a_123456.Shop.dbo.KhoHang
WHERE MaKhoHang = 1
UNION ALL
SELECT * FROM mssql_site_b_123456.Shop.dbo.KhoHang
WHERE MaKhoHang = 2;
GO

-- SanPham
CREATE VIEW dbo.SanPham AS
SELECT * FROM mssql_site_a_123456.Shop.dbo.SanPham
WHERE MaSanPham BETWEEN 10001 AND 60000
UNION ALL
SELECT * FROM mssql_site_b_123456.Shop.dbo.SanPham
WHERE MaSanPham BETWEEN 60001 AND 110000;
GO

-- KhachHang
CREATE VIEW dbo.KhachHang AS
SELECT * FROM mssql_site_a_123456.Shop.dbo.KhachHang
WHERE MaKhachHang BETWEEN 10001 AND 15000
UNION ALL
SELECT * FROM mssql_site_b_123456.Shop.dbo.KhachHang
WHERE MaKhachHang BETWEEN 15001 AND 20000;
GO

-- HoaDon
CREATE VIEW dbo.HoaDon AS
SELECT * FROM mssql_site_a_123456.Shop.dbo.HoaDon
WHERE MaHoaDon BETWEEN 10001 AND 110000
UNION ALL
SELECT * FROM mssql_site_b_123456.Shop.dbo.HoaDon
WHERE MaHoaDon BETWEEN 110001 AND 210000;
GO

-- ChiTietHoaDon
CREATE VIEW dbo.ChiTietHoaDon AS
SELECT * FROM mssql_site_a_123456.Shop.dbo.ChiTietHoaDon
WHERE MaHoaDon BETWEEN 10001 AND 110000
UNION ALL
SELECT * FROM mssql_site_b_123456.Shop.dbo.ChiTietHoaDon
WHERE MaHoaDon BETWEEN 110001 AND 210000;
GO