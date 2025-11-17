USE Shop;
GO
IF OBJECT_ID('dbo.HoaDon', 'V') IS NOT NULL DROP VIEW dbo.HoaDon;
IF OBJECT_ID('dbo.ChiTietHoaDon', 'V') IS NOT NULL DROP VIEW dbo.ChiTietHoaDon;
GO

-- View ghi phân tán: HoaDon
CREATE VIEW dbo.HoaDon AS
SELECT * FROM mssql_site_a_123456.Shop.dbo.HoaDon
WHERE MaHoaDon BETWEEN 10001 AND 110000
UNION ALL
SELECT * FROM mssql_site_b_123456.Shop.dbo.HoaDon
WHERE MaHoaDon BETWEEN 110001 AND 300000   -- mở rộng cho Lab 5
WITH CHECK OPTION;
GO

-- View ghi phân tán: ChiTietHoaDon
CREATE VIEW dbo.ChiTietHoaDon AS
SELECT * FROM mssql_site_a_123456.Shop.dbo.ChiTietHoaDon
WHERE MaHoaDon BETWEEN 10001 AND 110000
UNION ALL
SELECT * FROM mssql_site_b_123456.Shop.dbo.ChiTietHoaDon
WHERE MaHoaDon BETWEEN 110001 AND 300000
WITH CHECK OPTION;
GO