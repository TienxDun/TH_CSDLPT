use Shop;
GO
DROP TABLE dbo.ChiTietHoaDon;
DROP TABLE dbo.HoaDon;

CREATE TABLE dbo.HoaDon (
  MaHoaDon INT PRIMARY KEY,
  MaKhachHang INT FOREIGN KEY REFERENCES dbo.KhachHang(MaKhachHang),
  Ngay DATETIME2
);
CREATE TABLE dbo.ChiTietHoaDon (
  MaHoaDon INT FOREIGN KEY REFERENCES dbo.HoaDon(MaHoaDon),
  MaSanPham INT, -- KHÔNG CÓ FOREIGN KEY REFERENCES dbo.SanPham(MaSanPham)
  SoLuong INT,
  PRIMARY KEY (MaHoaDon, MaSanPham)
);
ALTER TABLE Shop.dbo.SanPham ADD TonKho INT NOT NULL DEFAULT 10;