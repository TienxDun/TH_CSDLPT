<?php
function fetchApi($url) {
  try {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3); // tránh treo
    $json = curl_exec($ch);
    if ($json === false) {
      $error = curl_error($ch);
      curl_close($ch);
      return ['error' => "cURL error: $error"];
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($status !== 200) {
      return ['error' => "HTTP status $status"];
    }
    return json_decode($json, true);
  } catch (Exception $e) {
    return ['error' => $e->getMessage()];
  }
}
$base = "http://api_php_123456:8080"; //Chú ý tên/port service chỗ này

// gọi dữ liệu từ API
$sanpham   = fetchApi("$base/sanpham");
//$khachhang = fetchApi("$base/khachhang");
//$hoadon    = fetchApi("$base/hoadon");

// Xử lý POST thêm sản phẩm
$successMessage = null;
if (isset($_POST['add_product'])) {
  $data = [
    'MaSanPham' => intval($_POST['MaSanPham']),
    'TenSanPham' => $_POST['TenSanPham'],
    'GiaBan' => intval($_POST['GiaBan']),
    'MaKhoHang' => intval($_POST['MaKhoHang'])
  ];
  $ch = curl_init("$base/sanpham");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  $response = curl_exec($ch);
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  $result = json_decode($response, true);
  if ($status == 201) {
    // Redirect để tránh resubmit
    header('Location: ui.php?success=' . urlencode($result['message']));
    exit;
  } else {
    $errorMessage = $result['error'] ?? 'Unknown error';
  }
}

// Lấy success từ query
if (isset($_GET['success'])) {
  $successMessage = htmlspecialchars($_GET['success']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản Lý Cửa Hàng</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 20px;
      color: #333;
    }
    .container {
      max-width: 1200px;
      margin: 0 auto;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h1, h2, h3 {
      color: #2c3e50;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    table th, table td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    table th {
      background-color: #3498db;
      color: white;
    }
    table tr:hover {
      background-color: #f1f1f1;
    }
    form {
      margin-bottom: 20px;
      padding: 15px;
      background: #ecf0f1;
      border-radius: 5px;
    }
    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }
    input, select {
      width: 100%;
      padding: 8px;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    button {
      background-color: #27ae60;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }
    button:hover {
      background-color: #229954;
    }
    .error {
      color: #e74c3c;
      font-weight: bold;
    }
    .success {
      color: #27ae60;
      font-weight: bold;
    }
    .section {
      margin-bottom: 30px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="section">
      <h2>Danh sách Sản phẩm</h2>
      <?php if (isset($sanpham['error'])): ?>
        <p class="error"><?= $sanpham['error'] ?></p>
      <?php else: ?>
        <table>
          <tr><th>ID</th><th>Tên</th><th>Giá</th></tr>
          <?php foreach ($sanpham as $sp): ?>
            <tr>
              <td><?= $sp['MaSanPham'] ?></td>
              <td><?= $sp['TenSanPham'] ?></td>
              <td><?= number_format($sp['GiaBan']) ?> VND</td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>

    <div class="section">
      <h2>Thêm sản phẩm mới</h2>
      <?php if ($successMessage): ?>
        <p class="success"><?= $successMessage ?></p>
      <?php endif; ?>
      <?php if (isset($errorMessage)): ?>
        <p class="error">Lỗi: <?= $errorMessage ?></p>
      <?php endif; ?>
      <form method="post" action="ui.php">
        <label>Mã sản phẩm: <input type="number" name="MaSanPham" placeholder="Ví dụ: 202" required></label>
        <label>Tên sản phẩm: <input type="text" name="TenSanPham" placeholder="Ví dụ: Sách toán" required></label>
        <label>Giá bán: <input type="number" name="GiaBan" placeholder="Ví dụ: 50000" required></label>
        <label>Mã kho hàng:
          <select name="MaKhoHang" required>
            <option value="1">1 - Kho Hà Nội</option>
            <option value="2">2 - Kho HCM</option>
          </select>
        </label>
        <button type="submit" name="add_product">Thêm sản phẩm</button>
      </form>
    </div>

    <div class="section">
      <h2>Tra cứu hóa đơn</h2>
      <form method="get" action="ui.php">
        <label>ID hóa đơn: <input type="number" name="id" placeholder="Nhập ID hóa đơn" required></label>
        <button type="submit">Xem</button>
      </form>
      <?php
      if (isset($_GET['id'])) {
          $hd = fetchApi("$base/hoadon/" . intval($_GET['id']));
          if (isset($hd['error'])) {
            echo "<p class='error'>{$hd['error']}</p>";
          } else {
            echo "<h3>Hóa đơn #{$hd['MaHoaDon']}</h3>";
            echo "<p><strong>Ngày:</strong> {$hd['Ngay']}</p>";
            echo "<p><strong>Khách hàng:</strong> {$hd['KhachHang']['TenKh']} - {$hd['KhachHang']['DiaChi']} - {$hd['KhachHang']['SoDienThoai']}</p>";
            echo "<h4>Chi tiết:</h4>";
            echo "<table><tr><th>Sản phẩm</th><th>Giá</th><th>SL</th><th>Thành tiền</th></tr>";
            foreach ($hd['ChiTiet'] as $ct) {
              echo "<tr><td>{$ct['TenSanPham']}</td><td>" . number_format($ct['GiaBan']) . " VND</td><td>{$ct['SoLuong']}</td><td>" . number_format($ct['ThanhTien']) . " VND</td></tr>";
            }
            echo "</table>";
            echo "<p><strong>Tổng tiền: " . number_format($hd['TongTien']) . " VND</strong></p>";
          }
      }
      ?>
    </div>
  </div>
</body>
</html>