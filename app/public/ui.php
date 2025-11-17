<?php
function fetchApi($url) {
  try {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // tránh treo
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
      $ct = fetchApi("$base/chitiethoadon/" . intval($_GET['id']));
      echo "<pre>" . print_r($ct, true) . "</pre>";
  }
  ?>
</body>
</html>