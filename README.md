# TH_CSDLPT

Dự án TH_CSDLPT là ứng dụng quản lý cơ sở dữ liệu đơn giản với API RESTful PHP và giao diện web, sử dụng SQL Server, container hóa bằng Docker.

## Cấu trúc dự án

- `docker-compose.yml`: Cấu hình Docker Compose (SQL Server, API PHP, UI PHP).
- `app/`: Mã nguồn PHP.
  - `Dockerfile`: Build image PHP.
  - `public/index.php`: Router API.
  - `routes/`: Handlers cho từng endpoint (sanpham.php, khachhang.php, etc.).
  - `common.php`: Kết nối DB và helpers.
  - `public/ui.php`: Giao diện web quản lý.
- `db/`: Scripts DB.
  - `init.sql`: Tạo tables (KhoHang, SanPham, KhachHang, HoaDon, ChiTietHoaDon).
  - `seed.sql`: Dữ liệu mẫu.

## Công nghệ sử dụng

- **PHP 8**: API và UI.
- **SQL Server**: Cơ sở dữ liệu.
- **Docker & Docker Compose**: Container hóa.
- **HTML/CSS/JS**: Giao diện web.

## Cài đặt và chạy

1. Cài đặt Docker và Docker Compose.

2. Clone repo:

   ```bash
   git clone https://github.com/TienxDun/TH_CSDLPT.git
   cd TH_CSDLPT
   ```

3. Set biến môi trường cho password SQL Server (hoặc edit docker-compose.yml).

4. Chạy:

   ```bash
   docker-compose up -d
   ```

5. Truy cập:
   - UI: `http://localhost:8081`
   - API: `http://localhost:8080`

## API Endpoints

- **Sản phẩm** (`/sanpham`): GET (list/all), POST (add), PUT (update), DELETE (remove).
- **Khách hàng** (`/khachhang`): GET, POST, PUT, DELETE.
- **Hóa đơn** (`/hoadon`): GET (list), POST (add).
- **Chi tiết hóa đơn** (`/chitiethoadon`): GET (by MaHoaDon), POST (add item).
- **Kho hàng** (`/khohang`): GET (list).

## Giao diện Web

UI tại `ui.php` với menu chuyển trang:

- **Sản phẩm**: Thêm, danh sách (với kho), edit, delete.
- **Khách hàng**: Thêm, danh sách, edit, delete.
- **Hóa đơn**: Thêm, danh sách, tra cứu chi tiết.
- **Chi tiết hóa đơn**: Xem chi tiết, thêm sản phẩm vào hóa đơn.
- **Kho hàng**: Danh sách kho.

## Dừng ứng dụng

```bash
docker-compose down
```

## Đóng góp

Tạo pull request hoặc liên hệ tác giả.

## Tác giả

- **TienxDun** - [GitHub](https://github.com/TienxDun)

## Giấy phép

MIT License.
