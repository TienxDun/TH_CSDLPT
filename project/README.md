# TH_CSDLPT - Quản Lý Cửa Hàng

Dự án này là một ứng dụng web đơn giản để quản lý cửa hàng, bao gồm quản lý sản phẩm, khách hàng, hóa đơn và kho hàng. Ứng dụng sử dụng PHP cho backend API, SQL Server cho cơ sở dữ liệu, và Docker để container hóa.

## Cấu Trúc Dự Án

```
project/
├── docker-compose.yml    # Cấu hình Docker Compose
├── app/
│   ├── common.php        # Kết nối DB và hàm tiện ích
│   ├── Dockerfile        # Dockerfile cho PHP app
│   └── public/
│       ├── index.php     # Router chính
│       └── ui.php        # Giao diện web đơn giản
│   └── routes/
│       ├── default.php   # Route mặc định
│       ├── sanpham.php   # API sản phẩm
│       ├── hoadon.php    # API hóa đơn (chưa triển khai)
│       ├── khachhang.php # API khách hàng (chưa triển khai)
│       ├── khohang.php   # API kho hàng (chưa triển khai)
│       └── chitiethoadon.php # API chi tiết hóa đơn (chưa triển khai)
└── db/
    ├── init.sql          # Script tạo bảng
    └── seed.sql          # Dữ liệu mẫu
```

## Yêu Cầu Hệ Thống

- Docker (phiên bản 20.10 trở lên)
- Docker Compose (phiên bản 1.29 trở lên)
- Biến môi trường: `MSSQL_SA_PASSWORD` (mật khẩu mạnh cho SQL Server SA, ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt)

## Cài Đặt và Chạy

### Bước 1: Kiểm tra yêu cầu hệ thống

Đảm bảo Docker và Docker Compose đã được cài đặt:

```bash
docker --version
docker-compose --version
```

Nếu chưa cài đặt, hãy tải và cài đặt từ [Docker website](https://www.docker.com/get-started).

### Bước 2: Clone repository

```bash
git clone https://github.com/TienxDun/TH_CSDLPT.git
cd TH_CSDLPT/project
```

### Bước 3: Thiết lập biến môi trường

Tạo file `.env` trong thư mục `project` với nội dung:

```env
MSSQL_SA_PASSWORD=YourStrongPassword123!
```

**Lưu ý:** Thay `YourStrongPassword123!` bằng mật khẩu mạnh của bạn. Mật khẩu này sẽ được sử dụng cho tài khoản SA của SQL Server.

### Bước 4: Chạy ứng dụng

```bash
docker-compose up --build
```

Lệnh này sẽ:

- Tải và xây dựng các container
- Khởi tạo SQL Server với cơ sở dữ liệu `Shop`
- Chạy PHP server cho API và giao diện web

**Lưu ý:** Lần đầu chạy có thể mất vài phút để tải images và khởi tạo database.

### Bước 5: Khởi tạo dữ liệu (tùy chọn)

Sau khi containers đã chạy, bạn có thể khởi tạo dữ liệu mẫu bằng cách chạy scripts từ host (vì sqlcmd không có sẵn trong container SQL Server):

```bash
# Chờ SQL Server khởi động (khoảng 30-60 giây)
sqlcmd -S localhost,14331 -U sa -P $env:MSSQL_SA_PASSWORD -i .\db\init.sql
sqlcmd -S localhost,14331 -U sa -P $env:MSSQL_SA_PASSWORD -f i:65001 -i .\db\seed.sql
```

**Lưu ý:** Trong PowerShell, sử dụng `$env:MSSQL_SA_PASSWORD` để truy cập biến môi trường. Đảm bảo biến `MSSQL_SA_PASSWORD` đã được thiết lập trong shell của bạn. Lệnh `-f i:65001` đảm bảo encoding UTF-8 cho dữ liệu Unicode trong seed.sql.

### Bước 6: Truy cập ứng dụng

- **API Backend:** <http://localhost:8080>
- **Giao diện Web:** <http://localhost:8081/ui.php>

### Dừng ứng dụng

Để dừng và xóa containers:

```bash
docker-compose down
```

Để dừng và giữ dữ liệu:

```bash
docker-compose down -v  # Xóa volumes (mất dữ liệu DB)
```

## Troubleshooting

- **Lỗi kết nối DB:** Đảm bảo biến `MSSQL_SA_PASSWORD` đúng và SQL Server đã khởi động hoàn toàn.
- **Port bị chiếm:** Nếu ports 8080, 8081, 14331 bị sử dụng, thay đổi trong `docker-compose.yml`.
- **Không thể truy cập:** Kiểm tra containers đang chạy với `docker-compose ps`.

## Cơ Sở Dữ Liệu

Dự án sử dụng SQL Server với cơ sở dữ liệu tên `Shop`. Các bảng chính:

- `KhoHang`: Thông tin kho hàng
- `SanPham`: Thông tin sản phẩm
- `KhachHang`: Thông tin khách hàng
- `HoaDon`: Thông tin hóa đơn
- `ChiTietHoaDon`: Chi tiết hóa đơn

## API Endpoints

### Sản Phẩm

- `GET /sanpham` - Lấy danh sách tất cả sản phẩm
- `GET /sanpham/{id}` - Lấy thông tin sản phẩm theo ID

### Các API khác (chưa triển khai)

- `/khachhang` - Quản lý khách hàng
- `/hoadon` - Quản lý hóa đơn
- `/khohang` - Quản lý kho hàng
- `/chitiethoadon` - Quản lý chi tiết hóa đơn

## Giao Diện Web

Giao diện web đơn giản tại `/ui.php` hiển thị danh sách sản phẩm từ API.

## Phát Triển

Để phát triển thêm:

- Triển khai các routes còn lại trong thư mục `routes/`
- Thêm validation và error handling
- Cải thiện giao diện web
- Thêm authentication

## Công Nghệ Sử Dụng

- **Backend:** PHP 8.4 với PDO
- **Database:** Microsoft SQL Server 2022
- **Container:** Docker & Docker Compose
- **Frontend:** HTML thuần (đơn giản)
