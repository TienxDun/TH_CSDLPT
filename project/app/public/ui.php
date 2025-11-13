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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Test API</title>
</head>
<body>
  <h1>Danh sách Sản phẩm</h1>
  <?php if (isset($sanpham['error'])): ?>
    <p style="color:red"><?= $sanpham['error'] ?></p>
  <?php else: ?>
    <table border="1">
      <tr><th>ID</th><th>Tên</th><th>Giá</th></tr>
      <?php foreach ($sanpham as $sp): ?>
        <tr>
          <td><?= $sp['MaSanPham'] ?></td>
          <td><?= $sp['TenSanPham'] ?></td>
          <td><?= $sp['GiaBan'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

  <h1>Tra cứu hóa đơn</h1>
  <form method="get" action="ui.php">
    <input type="number" name="id" placeholder="Nhập ID hóa đơn">
    <button type="submit">Xem</button>
  </form>
  <?php
  if (isset($_GET['id'])) {
      $hd = fetchApi("$base/hoadon/" . intval($_GET['id']));
      if (isset($hd['error'])) {
        echo "<p style='color:red'>{$hd['error']}</p>";
      } else {
        echo "<h2>Hóa đơn #{$hd['MaHoaDon']}</h2>";
        echo "<p>Ngày: {$hd['Ngay']}</p>";
        echo "<p>Khách hàng: {$hd['KhachHang']['TenKh']} - {$hd['KhachHang']['DiaChi']} - {$hd['KhachHang']['SoDienThoai']}</p>";
        echo "<h3>Chi tiết:</h3>";
        echo "<table border='1'><tr><th>Sản phẩm</th><th>Giá</th><th>SL</th><th>Thành tiền</th></tr>";
        foreach ($hd['ChiTiet'] as $ct) {
          echo "<tr><td>{$ct['TenSanPham']}</td><td>{$ct['GiaBan']}</td><td>{$ct['SoLuong']}</td><td>{$ct['ThanhTien']}</td></tr>";
        }
        echo "</table>";
        echo "<p><strong>Tổng tiền: {$hd['TongTien']}</strong></p>";
      }
  }
  ?>
</body>
</html>