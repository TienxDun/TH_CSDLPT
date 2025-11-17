USE Shop;
GO
-- Q4.1: Ghi trực tiếp vào site B
INSERT INTO mssql_site_b_123456.Shop.dbo.HoaDon (MaHoaDon, MaKhachHang, Ngay)
VALUES (230001, 16000, SYSDATETIME());

-- Q4.2: Ghi qua view DPV
INSERT INTO HoaDon (MaHoaDon, MaKhachHang, Ngay)
VALUES (230002, 16000, SYSDATETIME());
So sánh thời gian bằng:

SET STATISTICS TIME ON;
-- chạy hai lệnh trên riêng biệt để xem chênh lệch
SET STATISTICS TIME OFF;