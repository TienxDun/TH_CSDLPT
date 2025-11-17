USE Shop;
GO

-- Q3.1: Thêm hóa đơn mới (ghi vào site B vì > 110000)
INSERT INTO HoaDon (MaHoaDon, MaKhachHang, Ngay)
VALUES (220001, 15500, SYSDATETIME());

-- Q3.2: Thêm chi tiết hóa đơn tương ứng
INSERT INTO ChiTietHoaDon (MaHoaDon, MaSanPham, SoLuong)
VALUES (220001, 10200, 3);

-- Q3.3: Cập nhật số lượng qua view
UPDATE ChiTietHoaDon
SET SoLuong = SoLuong + 2
WHERE MaHoaDon = 220001 AND MaSanPham = 10200;

-- Q3.4: Xóa chi tiết hóa đơn
DELETE FROM ChiTietHoaDon
WHERE MaHoaDon = 220001 AND MaSanPham = 10200;

-- Q3.5: Kiểm tra phân bố dữ liệu
SELECT 'Site A' AS Site, COUNT(*) AS SoHD
FROM mssql_site_a_123456.Shop.dbo.HoaDon
UNION ALL
SELECT 'Site B', COUNT(*) 
FROM mssql_site_b_123456.Shop.dbo.HoaDon;
