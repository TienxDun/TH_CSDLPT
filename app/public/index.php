<?php
require_once __DIR__ . '/../src/ApiHelper.php';
require_once __DIR__ . '/../src/Transaction.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);

  // Kiểm tra và trích xuất dữ liệu cần thiết
  $maKhachHang = $data['maKhachHang'] ?? null;
  $orders = $data['orders'] ?? [];

  if ($maKhachHang === null || !is_array($orders) || count($orders) === 0) {
    // Trả về lỗi nếu thiếu dữ liệu quan trọng
    $result = [
      'status' => 'error',
      'message' => 'Dữ liệu đầu vào không hợp lệ: Thiếu MaKhachHang hoặc Orders.'
    ];
  } else {
    // Gọi hàm buyFromSites mới với 2 tham số
    $result = buyFromSites($maKhachHang, $orders);
  }

  sendJsonResponse($result);
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  switch ($_SERVER['REQUEST_URI']){
    case '/api/SanPham/random':
      sendJsonResponse(getRandomSanPham());
      break;
    case '/api/KhachHang/random':
      sendJsonResponse(getRandomKhachHang());
      break;
  }
}
$result=[
  'error' => 'API chưa hỗ trợ'
];
sendJsonResponse($result);