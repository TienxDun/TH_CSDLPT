CREATE DATABASE Shop;
GO
USE Shop;
GO

CREATE TABLE dbo.KhoHang (
  MaKhoHang INT PRIMARY KEY,
  TenKhoHang NVARCHAR(50),
  DiaChi NVARCHAR(200)
);

CREATE TABLE dbo.SanPham (
  MaSanPham INT PRIMARY KEY,
  TenSanPham NVARCHAR(100),
  GiaBan INT,
  MaKhoHang INT FOREIGN KEY REFERENCES dbo.KhoHang(MaKhoHang)
);

CREATE TABLE dbo.KhachHang (
  MaKhachHang INT PRIMARY KEY,
  TenKh NVARCHAR(100),
  DiaChi NVARCHAR(200),
  SoDienThoai CHAR(10)
);

CREATE TABLE dbo.HoaDon (
  MaHoaDon INT PRIMARY KEY,
  MaKhachHang INT FOREIGN KEY REFERENCES dbo.KhachHang(MaKhachHang),
  Ngay DATETIME2
);

CREATE TABLE dbo.ChiTietHoaDon (
  MaHoaDon INT FOREIGN KEY REFERENCES dbo.HoaDon(MaHoaDon),
  MaSanPham INT FOREIGN KEY REFERENCES dbo.SanPham(MaSanPham),
  SoLuong INT,
  PRIMARY KEY (MaHoaDon, MaSanPham)
);

PRINT 'Database initialized successfully';