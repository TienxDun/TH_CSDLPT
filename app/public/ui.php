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
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
    h1 { color: #333; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; background: white; }
    th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
    th { background-color: #f2f2f2; }
    form { background: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
    input { display: block; width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
    button { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background-color: #218838; }
    button:disabled { background-color: #ccc; cursor: not-allowed; }
    .spinner { display: none; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 20px; height: 20px; animation: spin 1s linear infinite; margin-left: 10px; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    #message { margin-top: 10px; font-weight: bold; }
    .error { color: red; }
    .success { color: green; }
  </style>
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

  <h1>Thêm Khách hàng mới</h1>
  <form id="khachhangForm">
    <input type="number" id="maKhachHang" placeholder="Mã khách hàng" required><br>
    <input type="text" id="tenKh" placeholder="Tên khách hàng" required><br>
    <input type="text" id="diaChi" placeholder="Địa chỉ" required><br>
    <input type="text" id="soDienThoai" placeholder="Số điện thoại" required><br>
    <button type="submit" id="submitBtn">Thêm <div class="spinner" id="spinner"></div></button>
  </form>
  <p id="message"></p>

  <h1>Danh sách Khách hàng</h1>
  <div id="khachhangList"></div>

  <h1>Chỉnh sửa Khách hàng</h1>
  <form id="editForm" style="display:none;">
    <input type="number" id="editMaKhachHang" placeholder="Mã khách hàng" readonly><br>
    <input type="text" id="editTenKh" placeholder="Tên khách hàng" required><br>
    <input type="text" id="editDiaChi" placeholder="Địa chỉ" required><br>
    <input type="text" id="editSoDienThoai" placeholder="Số điện thoại" required><br>
    <button type="submit">Cập nhật</button>
    <button type="button" onclick="cancelEdit()">Hủy</button>
  </form>

  <script>
    document.getElementById('khachhangForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('submitBtn');
      const spinner = document.getElementById('spinner');
      const message = document.getElementById('message');
      btn.disabled = true;
      spinner.style.display = 'inline-block';
      message.textContent = '';
      message.className = '';

      const data = {
        MaKhachHang: document.getElementById('maKhachHang').value,
        TenKh: document.getElementById('tenKh').value,
        DiaChi: document.getElementById('diaChi').value,
        SoDienThoai: document.getElementById('soDienThoai').value
      };
      try {
        const response = await fetch('http://localhost:8080/khachhang', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await response.json();
        message.textContent = result.message || result.error;
        message.className = result.message ? 'success' : 'error';
        if (result.message) loadKhachHang(); // Reload list after add
      } catch (error) {
        message.textContent = 'Lỗi: ' + error.message;
        message.className = 'error';
      } finally {
        btn.disabled = false;
        spinner.style.display = 'none';
      }
    });

    async function loadKhachHang() {
      try {
        const response = await fetch('http://localhost:8080/khachhang');
        const khachhang = await response.json();
        let html = '<table><tr><th>ID</th><th>Tên</th><th>Địa chỉ</th><th>SĐT</th><th>Actions</th></tr>';
        khachhang.forEach(kh => {
          html += `<tr><td>${kh.MaKhachHang}</td><td>${kh.TenKh}</td><td>${kh.DiaChi}</td><td>${kh.SoDienThoai}</td><td><button onclick="editKhachHang(${kh.MaKhachHang})">Edit</button> <button onclick="deleteKhachHang(${kh.MaKhachHang})">Delete</button></td></tr>`;
        });
        html += '</table>';
        document.getElementById('khachhangList').innerHTML = html;
      } catch (error) {
        document.getElementById('khachhangList').innerHTML = 'Lỗi tải danh sách';
      }
    }

    function editKhachHang(id) {
      fetch(`http://localhost:8080/khachhang/${id}`)
        .then(r => r.json())
        .then(kh => {
          document.getElementById('editMaKhachHang').value = kh.MaKhachHang;
          document.getElementById('editTenKh').value = kh.TenKh;
          document.getElementById('editDiaChi').value = kh.DiaChi;
          document.getElementById('editSoDienThoai').value = kh.SoDienThoai;
          document.getElementById('editForm').style.display = 'block';
        });
    }

    function cancelEdit() {
      document.getElementById('editForm').style.display = 'none';
    }

    document.getElementById('editForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const id = document.getElementById('editMaKhachHang').value;
      const data = {
        TenKh: document.getElementById('editTenKh').value,
        DiaChi: document.getElementById('editDiaChi').value,
        SoDienThoai: document.getElementById('editSoDienThoai').value
      };
      try {
        const response = await fetch(`http://localhost:8080/khachhang/${id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await response.json();
        alert(result.message || result.error);
        loadKhachHang();
        cancelEdit();
      } catch (error) {
        alert('Lỗi: ' + error.message);
      }
    });

    async function deleteKhachHang(id) {
      if (confirm('Xóa khách hàng này?')) {
        try {
          const response = await fetch(`http://localhost:8080/khachhang/${id}`, { method: 'DELETE' });
          const result = await response.json();
          alert(result.message || result.error);
          loadKhachHang();
        } catch (error) {
          alert('Lỗi: ' + error.message);
        }
      }
    }

    loadKhachHang(); // Load list on page load
  </script>

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