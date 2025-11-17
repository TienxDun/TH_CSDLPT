CREATE DATABASE Shop;
GO

USE Shop;
GO
-- Linked server tới site A
EXEC sp_addlinkedserver 
   @server = N'mssql_site_a_123456',
   @provider = N'MSOLEDBSQL',
   @srvproduct = N'',
   @datasrc = N'mssql_site_a_123456';

EXEC sp_addlinkedsrvlogin 
   @rmtsrvname = N'mssql_site_a_123456',
   @useself = 'false',
   @locallogin = NULL,
   @rmtuser = 'sa',
   @rmtpassword = 'Your@STROng!Pass#Word';

-- Linked server tới site B
EXEC sp_addlinkedserver 
   @server = N'mssql_site_b_123456',
   @provider = N'MSOLEDBSQL',
   @srvproduct = N'',
   @datasrc = N'mssql_site_b_123456';

EXEC sp_addlinkedsrvlogin 
   @rmtsrvname = N'mssql_site_b_123456',
   @useself = 'false',
   @locallogin = NULL,
   @rmtuser = 'sa',
   @rmtpassword = 'Your@STROng!Pass#Word';
GO
-- View toàn cục
CREATE VIEW dbo.SanPham AS
SELECT * FROM mssql_site_a_123456.Shop.dbo.SanPham
UNION ALL
SELECT * FROM mssql_site_b_123456.Shop.dbo.SanPham;
GO

CREATE VIEW dbo.KhoHang AS
SELECT * FROM mssql_site_a_123456.Shop.dbo.KhoHang
UNION ALL
SELECT * FROM mssql_site_b_123456.Shop.dbo.KhoHang;
GO

CREATE VIEW dbo.KhachHang AS
SELECT * FROM mssql_site_a_123456.Shop.dbo.KhachHang
UNION ALL
SELECT * FROM mssql_site_b_123456.Shop.dbo.KhachHang;
GO

CREATE VIEW dbo.HoaDon AS
SELECT * FROM mssql_site_a_123456.Shop.dbo.HoaDon
UNION ALL
SELECT * FROM mssql_site_b_123456.Shop.dbo.HoaDon;