# TH_CSDLPT

Dự án TH_CSDLPT là một ứng dụng web đơn giản được xây dựng bằng PHP và sử dụng cơ sở dữ liệu, được container hóa bằng Docker để dễ dàng triển khai và quản lý.

## Cấu trúc dự án

- `docker-compose.yml`: Cấu hình Docker Compose để chạy ứng dụng và cơ sở dữ liệu.
- `app/`: Thư mục chứa mã nguồn ứng dụng PHP.
  - `Dockerfile`: Cấu hình Docker cho ứng dụng PHP.
  - `public/index.php`: File chính của ứng dụng web.
- `db/`: Thư mục chứa dữ liệu cơ sở dữ liệu.
  - `init.sql`: Script khởi tạo cơ sở dữ liệu.
  - `seed.sql`: Script chèn dữ liệu mẫu.

## Công nghệ sử dụng

- **PHP**: Ngôn ngữ lập trình phía server.
- **Docker & Docker Compose**: Container hóa ứng dụng.
- **Cơ sở dữ liệu**: (Có thể là MySQL hoặc PostgreSQL, tùy thuộc vào cấu hình).

## Cài đặt và chạy

1. Đảm bảo bạn đã cài đặt Docker và Docker Compose trên máy tính.

2. Clone repository này về máy:

   ```bash
   git clone https://github.com/TienxDun/TH_CSDLPT_New.git
   cd TH_CSDLPT_New
   ```

3. Chạy ứng dụng bằng Docker Compose:

   ```bash
   docker-compose up -d
   ```

4. Truy cập ứng dụng tại `http://localhost` (hoặc cổng được cấu hình trong docker-compose.yml).

## Dừng ứng dụng

Để dừng ứng dụng, chạy:

```bash
docker-compose down
```

## Đóng góp

Nếu bạn muốn đóng góp cho dự án, vui lòng tạo một pull request hoặc liên hệ với tác giả.

## Tác giả

- **TienxDun** - [GitHub](https://github.com/TienxDun)

## Giấy phép

Dự án này được phân phối dưới giấy phép MIT. Xem file `LICENSE` để biết thêm chi tiết.
