<?php
require_once __DIR__ . '/../src/AppHelper.php';

$args = array_slice($argv, 1);

$qtyArgs = [];

foreach ($args as $arg) {
  if (is_numeric($arg) && $arg >= 0) {
    $qtyArgs[] = (int)$arg;
  }
}

if (empty($qtyArgs)) {
  echo "Lỗi: Vui lòng cung cấp số lượng (qty) cho ít nhất 1 sản phẩm.\n";
  echo "Cú pháp: php test_buy.php [qty1] [qty2] ... [qtyN]\n";
  echo "Ví dụ thành công: php test_buy.php 1 2 3\n";
  echo "Ví dụ thất bại (hết hàng): php test_buy.php 1 99999\n";
  exit(1);
}

// 2. Tạo dữ liệu mua hàng (orders)
$orders = [];
echo "Danh sách mua hàng:\n";
foreach ($qtyArgs as $index => $qty) {
  $sp = getSP();
  $orders[] = [
    'MaSanPham' => $sp['MaSanPham'],
    'TenSanPham' => $sp['TenSanPham'],
    'qty' => $qty
  ];
  $no = $index + 1;
  echo "  - Sản phẩm #{$no}: Mã {$sp['MaSanPham']} {$sp['TenSanPham']} (Qty: {$qty}, TonKho: {$sp['TonKho']})\n";
}

// 3. Chọn Khách hàng ngẫu nhiên
$kh = GetKH();

// 4. Gọi API
echo "--------------------------------------------------------\n";
echo "Gọi API cho khach hang: {$kh['MaKhachHang']} {$kh['TenKh']}\n";
echo "--------------------------------------------------------\n";

try {
  $jsonResponse = callBuyAPI($kh['MaKhachHang'], $orders);

  // Định dạng JSON cho đẹp khi in ra CLI
  $responseData = json_decode($jsonResponse, true);
  $prettyJson = json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

  echo $prettyJson . "\n";
} catch (Exception $e) {
  echo "Lỗi gọi API: " . $e->getMessage() . "\n";
}