<?php
// Bắt đầu bằng việc bao gồm file kết nối CSDL và các hàm tiện ích
if (!isset($_ENV['DB_HOST']) || !isset($_ENV['DB_PORT']) || !isset($_ENV['DB_NAME']) || !isset($_ENV['DB_USER']) || !isset($_ENV['DB_PASS'])) {
  cli_error("Thiếu các biến môi trường (DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS). Vui lòng kiểm tra file cấu hình Docker.");
}

$dsn = "sqlsrv:Server=" . $_ENV['DB_HOST'] . "," . $_ENV['DB_PORT'] . ";Database=" . $_ENV['DB_NAME']
  . ";TrustServerCertificate=1";
global $pdo;

try {
  $serverName = "{$_ENV['DB_HOST']}:{$_ENV['DB_PORT']}";
  $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);
  cli_log("Kết nối CSDL $serverName thành công.");
} catch (Exception $e) {
  cli_error("Lỗi kết nối CSDL $serverName: " . $e->getMessage());
}

// Số lượng bản ghi cần tạo
$total_records = 200000;
$batch_size = 5000;

// 1. Chuẩn bị dữ liệu cần thiết (Lấy danh sách PK hợp lệ)
try {
  // Lấy tất cả MaHoaDon
  $stmt_hd = $pdo->query("SELECT MaHoaDon FROM HoaDon");
  $ma_hoa_don_list = $stmt_hd->fetchAll(PDO::FETCH_COLUMN, 0);
  $count_hd = count($ma_hoa_don_list);

  // Lấy tất cả MaSanPham
  $stmt_sp = $pdo->query("SELECT MaSanPham FROM SanPham");
  $ma_san_pham_list = $stmt_sp->fetchAll(PDO::FETCH_COLUMN, 0);
  $count_sp = count($ma_san_pham_list);

  if ($count_hd === 0 || $count_sp === 0) {
    cli_error('Không có dữ liệu HoaDon hoặc SanPham để tạo CTHD.');
  }

  // Xóa dữ liệu cũ trước khi chèn
  $pdo->exec("DELETE FROM ChiTietHoaDon");

} catch (Exception $e) {
  cli_error('Lỗi truy vấn dữ liệu nguồn: ' . $e->getMessage());
}

// 2. Chuẩn bị truy vấn INSERT (Sử dụng Prepared Statement)
cli_log("Khởi tạo $total_records dữ liệu cho CTHD...");
$unique_details = [];
// Vòng lặp tạo 200,000 cặp duy nhất.
while (count($unique_details) < $total_records) {
  // Chọn ngẫu nhiên: đảm bảo chỉ số hợp lệ
  $random_ma_hd = $ma_hoa_don_list[array_rand($ma_hoa_don_list)];
  $random_ma_sp = $ma_san_pham_list[array_rand($ma_san_pham_list)];
  $key = $random_ma_hd . '-' . $random_ma_sp;

  // Chỉ thêm nếu cặp (HD, SP) chưa tồn tại
  if (!isset($unique_details[$key])) {
    $unique_details[$key] = [
      'MaHoaDon' => $random_ma_hd,
      'MaSanPham' => $random_ma_sp,
      'SoLuong' => rand(1, 10)
    ];
  }
}
$details_to_insert = array_values($unique_details);
cli_log("Đã tạo $total_records cặp dữ liệu duy nhất. Bắt đầu sắp xếp...");

// Thêm bước sắp xếp: SORT mảng theo MaHoaDon
usort($details_to_insert, function($a, $b) {
  // So sánh MaHoaDon (cột đầu tiên của Clustered Key)
  if ($a['MaHoaDon'] == $b['MaHoaDon']) {
    // Nếu MaHoaDon bằng nhau, so sánh MaSanPham (cột thứ hai)
    return $a['MaSanPham'] <=> $b['MaSanPham'];
  }
  return $a['MaHoaDon'] <=> $b['MaHoaDon'];
});

cli_log("Sắp xếp hoàn tất. Bắt đầu chèn.");

// Dùng prepared statement để chèn nhanh hơn và an toàn hơn
$sql_insert = "INSERT INTO ChiTietHoaDon (MaHoaDon, MaSanPham, SoLuong) VALUES (?, ?, ?)";
$stmt_insert = $pdo->prepare($sql_insert);

$start_time = microtime(true);
$inserted_count = 0;
$batch_start_time = $start_time;

// 3. Vòng lặp chèn dữ liệu
try {
  $pdo->exec('ALTER TABLE ChiTietHoaDon NOCHECK CONSTRAINT ALL');
  // Bắt đầu transaction để tăng tốc độ chèn
  $pdo->beginTransaction();

  foreach ($details_to_insert as $i => $detail) {
    // 3.2. Thực thi chèn (Sử dụng dữ liệu đã đảm bảo duy nhất)
    $stmt_insert = $pdo->prepare("INSERT INTO ChiTietHoaDon (MaHoaDon, MaSanPham, SoLuong) VALUES (?, ?, ?)");
    $stmt_insert->execute([$detail['MaHoaDon'], $detail['MaSanPham'], $detail['SoLuong']]);
    $inserted_count++;

    // 3.3. Kiểm tra tiến trình
    if ($inserted_count % $batch_size === 0) {
      $current_time = microtime(true);
      $elapsed_batch = $current_time - $batch_start_time;
      $elapsed_total = $current_time - $start_time;

      $msg = sprintf("Tạo dữ liệu... %s, batch_time=%s, total_time=%s",
        $inserted_count . '/' . $total_records,
        round($elapsed_batch, 2) . 's',
        round($elapsed_total, 2) . 's'
      );
      cli_log($msg);
      flush();

      $batch_start_time = $current_time;
    }
  }

  // Commit transaction sau khi chèn xong tất cả
  $pdo->commit();

  $end_time = microtime(true);
  $total_time = $end_time - $start_time;

  $msg = sprintf("Tạo dữ liệu ChiTietHoaDon hoàn thành (%s bản ghi)! Tổng thời gian: %s",
    $inserted_count,
    format_seconds_to_hms($total_time)
  );
  cli_log($msg);
} catch (Exception $e) {
  // Nếu có lỗi, rollback transaction và báo lỗi
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }
  responseError(500, 'Lỗi khi chèn dữ liệu: ' . $e->getMessage());
} finally {
  $pdo->exec('ALTER TABLE ChiTietHoaDon CHECK CONSTRAINT ALL');
}

function cli_log($message) {
  echo $message . "\n";
  flush();
}

function cli_error($message) {
  cli_log("\n[ERROR] " . $message);
  exit(1);
}
function format_seconds_to_hms($total_seconds) {
  $total_seconds = (int)$total_seconds;
  $hours = (int)($total_seconds / 3600);
  $minutes = (((int)($total_seconds / 60)) % 60);
  $seconds = $total_seconds % 60;
  return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}