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
//$sanpham   = fetchApi("$base/sanpham");
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
    .menu { background: white; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
    .menu button { margin-right: 10px; }
    .page { display: none; }
    .active { display: block; }
  </style>
</head>
<body>
  <h1>Quản lý CSDL</h1>
  <div class="menu">
    <button data-page="sanpham">Sản phẩm</button>
    <button data-page="khachhang">Khách hàng</button>
    <button data-page="hoadon">Hóa đơn</button>
    <button data-page="chitiethoadon">Chi tiết hóa đơn</button>
    <button data-page="khohang">Kho hàng</button>
  </div>
  <div id="sanpham" class="page active">
  <h1>Thêm Sản phẩm mới</h1>
  <form id="sanphamForm">
    <input type="number" id="maSanPham" placeholder="Mã sản phẩm" required><br>
    <input type="text" id="tenSanPham" placeholder="Tên sản phẩm" required><br>
    <input type="number" id="gia" placeholder="Giá" required><br>
    <input type="number" id="maKhoHang" placeholder="Mã kho hàng" required><br>
    <button type="submit" id="submitSanPhamBtn">Thêm <div class="spinner" id="spinnerSanPham"></div></button>
  </form>
  <p id="messageSanPham"></p>

  <h1>Danh sách Sản phẩm</h1>
  <div id="sanphamList"></div>

  <h1>Chỉnh sửa Sản phẩm</h1>
  <form id="editSanPhamForm" style="display:none;">
    <input type="number" id="editMaSanPham" placeholder="Mã sản phẩm" readonly><br>
    <input type="text" id="editTenSanPham" placeholder="Tên sản phẩm" required><br>
    <input type="number" id="editGia" placeholder="Giá" required><br>
    <input type="number" id="editMaKhoHang" placeholder="Mã kho hàng" required><br>
    <button type="submit">Cập nhật</button>
    <button type="button" onclick="cancelEditSanPham()">Hủy</button>
  </form>

  </div>

  <div id="khachhang" class="page">
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
    function showPage(pageId) {
      const pages = document.querySelectorAll('.page');
      pages.forEach(page => page.classList.remove('active'));
      document.getElementById(pageId).classList.add('active');
    }

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

    // SanPham functions
    async function loadSanPham() {
      try {
        const response = await fetch('http://localhost:8080/sanpham');
        const sanpham = await response.json();
        let html = '<table><tr><th>ID</th><th>Tên</th><th>Giá</th><th>Kho hàng</th><th>Actions</th></tr>';
        sanpham.forEach(sp => {
          html += `<tr><td>${sp.MaSanPham}</td><td>${sp.TenSanPham}</td><td>${sp.GiaBan}</td><td>${sp.TenKhoHang || 'N/A'}</td><td><button onclick="editSanPham(${sp.MaSanPham})">Edit</button> <button onclick="deleteSanPham(${sp.MaSanPham})">Delete</button></td></tr>`;
        });
        html += '</table>';
        document.getElementById('sanphamList').innerHTML = html;
      } catch (error) {
        document.getElementById('sanphamList').innerHTML = 'Lỗi tải danh sách';
      }
    }

    function editSanPham(id) {
      fetch(`http://localhost:8080/sanpham/${id}`)
        .then(r => r.json())
        .then(sp => {
          document.getElementById('editMaSanPham').value = sp.MaSanPham;
          document.getElementById('editTenSanPham').value = sp.TenSanPham;
          document.getElementById('editGia').value = sp.GiaBan;
          document.getElementById('editMaKhoHang').value = sp.MaKhoHang;
          document.getElementById('editSanPhamForm').style.display = 'block';
        });
    }

    function cancelEditSanPham() {
      document.getElementById('editSanPhamForm').style.display = 'none';
    }

    async function deleteSanPham(id) {
      if (confirm('Xóa sản phẩm này?')) {
        try {
          const response = await fetch(`http://localhost:8080/sanpham/${id}`, { method: 'DELETE' });
          const result = await response.json();
          alert(result.message || result.error);
          loadSanPham();
        } catch (error) {
          alert('Lỗi: ' + error.message);
        }
      }
    }

    // HoaDon functions
    async function loadHoaDon() {
      try {
        const response = await fetch('http://localhost:8080/hoadon');
        const hoadon = await response.json();
        let html = '<table><tr><th>ID</th><th>Khách hàng</th><th>Ngày</th></tr>';
        hoadon.forEach(hd => {
          html += `<tr><td>${hd.MaHoaDon}</td><td>${hd.TenKh}</td><td>${hd.Ngay}</td></tr>`;
        });
        html += '</table>';
        document.getElementById('hoadonList').innerHTML = html;
      } catch (error) {
        document.getElementById('hoadonList').innerHTML = 'Lỗi tải danh sách';
      }
    }

    async function loadKhoHang() {
      try {
        const response = await fetch('http://localhost:8080/khohang');
        const khohang = await response.json();
        let html = '<table><tr><th>ID</th><th>Tên kho</th><th>Địa chỉ</th></tr>';
        khohang.forEach(kh => {
          html += `<tr><td>${kh.MaKhoHang}</td><td>${kh.TenKhoHang}</td><td>${kh.DiaChi}</td></tr>`;
        });
        html += '</table>';
        document.getElementById('khohangList').innerHTML = html;
      } catch (error) {
        document.getElementById('khohangList').innerHTML = 'Lỗi tải danh sách';
      }
    }

    window.addEventListener('DOMContentLoaded', () => {
      // Add event listeners for menu buttons
      document.querySelectorAll('[data-page]').forEach(btn => {
        btn.addEventListener('click', () => showPage(btn.dataset.page));
      });
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

    function showPage(pageId) {
      const pages = document.querySelectorAll('.page');
      pages.forEach(page => page.classList.remove('active'));
      document.getElementById(pageId).classList.add('active');
    }

    // SanPham functions
    document.getElementById('sanphamForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('submitSanPhamBtn');
      const spinner = document.getElementById('spinnerSanPham');
      const message = document.getElementById('messageSanPham');
      btn.disabled = true;
      spinner.style.display = 'inline-block';
      message.textContent = '';
      message.className = '';

      const data = {
        MaSanPham: document.getElementById('maSanPham').value,
        TenSanPham: document.getElementById('tenSanPham').value,
        Gia: document.getElementById('gia').value,
        MaKhoHang: document.getElementById('maKhoHang').value
      };
      try {
        const response = await fetch('http://localhost:8080/sanpham', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await response.json();
        message.textContent = result.message || result.error;
        message.className = result.message ? 'success' : 'error';
        if (result.message) loadSanPham();
      } catch (error) {
        message.textContent = 'Lỗi: ' + error.message;
        message.className = 'error';
      } finally {
        btn.disabled = false;
        spinner.style.display = 'none';
      }
    });

    async function loadSanPham() {
      try {
        const response = await fetch('http://localhost:8080/sanpham');
        const sanpham = await response.json();
        let html = '<table><tr><th>ID</th><th>Tên</th><th>Giá</th><th>Kho hàng</th><th>Actions</th></tr>';
        sanpham.forEach(sp => {
          html += `<tr><td>${sp.MaSanPham}</td><td>${sp.TenSanPham}</td><td>${sp.GiaBan}</td><td>${sp.TenKhoHang || 'N/A'}</td><td><button onclick="editSanPham(${sp.MaSanPham})">Edit</button> <button onclick="deleteSanPham(${sp.MaSanPham})">Delete</button></td></tr>`;
        });
        html += '</table>';
        document.getElementById('sanphamList').innerHTML = html;
      } catch (error) {
        document.getElementById('sanphamList').innerHTML = 'Lỗi tải danh sách';
      }
    }

    function editSanPham(id) {
      fetch(`http://localhost:8080/sanpham/${id}`)
        .then(r => r.json())
        .then(sp => {
          document.getElementById('editMaSanPham').value = sp.MaSanPham;
          document.getElementById('editTenSanPham').value = sp.TenSanPham;
          document.getElementById('editGia').value = sp.GiaBan;
          document.getElementById('editMaKhoHang').value = sp.MaKhoHang;
          document.getElementById('editSanPhamForm').style.display = 'block';
        });
    }

    function cancelEditSanPham() {
      document.getElementById('editSanPhamForm').style.display = 'none';
    }

    document.getElementById('editSanPhamForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const id = document.getElementById('editMaSanPham').value;
      const data = {
        TenSanPham: document.getElementById('editTenSanPham').value,
        Gia: document.getElementById('editGia').value,
        MaKhoHang: document.getElementById('editMaKhoHang').value
      };
      try {
        const response = await fetch(`http://localhost:8080/sanpham/${id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await response.json();
        alert(result.message || result.error);
        loadSanPham();
        cancelEditSanPham();
      } catch (error) {
        alert('Lỗi: ' + error.message);
      }
    });

    async function deleteSanPham(id) {
      if (confirm('Xóa sản phẩm này?')) {
        try {
          const response = await fetch(`http://localhost:8080/sanpham/${id}`, { method: 'DELETE' });
          const result = await response.json();
          alert(result.message || result.error);
          loadSanPham();
        } catch (error) {
          alert('Lỗi: ' + error.message);
        }
      }
    }

    loadSanPham(); // Load list on page load

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

    // HoaDon functions
    document.getElementById('hoadonForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('submitHoaDonBtn');
      const spinner = document.getElementById('spinnerHoaDon');
      const message = document.getElementById('messageHoaDon');
      btn.disabled = true;
      spinner.style.display = 'inline-block';
      message.textContent = '';
      message.className = '';

      const data = {
        MaKhachHang: document.getElementById('maKhachHangHD').value,
        Ngay: document.getElementById('ngay').value.replace('T', ' ') + ':00'
      };
      try {
        const response = await fetch('http://localhost:8080/hoadon', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await response.json();
        message.textContent = (result.message || result.error) + (result.MaHoaDon ? ` (Mã: ${result.MaHoaDon})` : '');
        message.className = result.message ? 'success' : 'error';
        if (result.message) loadHoaDon();
      } catch (error) {
        message.textContent = 'Lỗi: ' + error.message;
        message.className = 'error';
      } finally {
        btn.disabled = false;
        spinner.style.display = 'none';
      }
    });

    async function loadHoaDon() {
      try {
        const response = await fetch('http://localhost:8080/hoadon');
        const hoadon = await response.json();
        let html = '<table><tr><th>ID</th><th>Khách hàng</th><th>Ngày</th></tr>';
        hoadon.forEach(hd => {
          html += `<tr><td>${hd.MaHoaDon}</td><td>${hd.TenKh}</td><td>${hd.Ngay}</td></tr>`;
        });
        html += '</table>';
        document.getElementById('hoadonList').innerHTML = html;
      } catch (error) {
        document.getElementById('hoadonList').innerHTML = 'Lỗi tải danh sách';
      }
    }

    document.getElementById('tracuuForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const id = document.getElementById('idTracuu').value;
      try {
        const response = await fetch(`http://localhost:8080/chitiethoadon/${id}`);
        const ct = await response.json();
        let html = '<h2>Chi tiết hóa đơn</h2><table><tr><th>Sản phẩm</th><th>Giá</th><th>Số lượng</th></tr>';
        ct.forEach(item => {
          html += `<tr><td>${item.TenSanPham}</td><td>${item.GiaBan}</td><td>${item.SoLuong}</td></tr>`;
        });
        html += '</table>';
        document.getElementById('chitietResult').innerHTML = html;
      } catch (error) {
        document.getElementById('chitietResult').innerHTML = 'Lỗi: ' + error.message;
      }
    });

    loadHoaDon(); // Load list on page load
    loadKhoHang(); // Load list on page load

    // ChiTietHoaDon functions
    document.getElementById('xemChiTietForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const maHoaDon = document.getElementById('maHoaDonXem').value;
      try {
        const response = await fetch(`http://localhost:8080/chitiethoadon/${maHoaDon}`);
        const ct = await response.json();
        let html = '<h2>Chi tiết hóa đơn</h2><table><tr><th>Sản phẩm</th><th>Giá</th><th>Số lượng</th></tr>';
        ct.forEach(item => {
          html += `<tr><td>${item.TenSanPham}</td><td>${item.GiaBan}</td><td>${item.SoLuong}</td></tr>`;
        });
        html += '</table>';
        document.getElementById('chitietResultXem').innerHTML = html;
      } catch (error) {
        document.getElementById('chitietResultXem').innerHTML = 'Lỗi: ' + error.message;
      }
    });

    document.getElementById('themChiTietForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('submitChiTietBtn');
      const spinner = document.getElementById('spinnerChiTiet');
      const message = document.getElementById('messageChiTiet');
      btn.disabled = true;
      spinner.style.display = 'inline-block';
      message.textContent = '';
      message.className = '';

      const data = {
        MaHoaDon: document.getElementById('maHoaDonThem').value,
        MaSanPham: document.getElementById('maSanPhamThem').value,
        SoLuong: document.getElementById('soLuongThem').value
      };
      try {
        const response = await fetch('http://localhost:8080/chitiethoadon', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await response.json();
        message.textContent = result.message || result.error;
        message.className = result.message ? 'success' : 'error';
      } catch (error) {
        message.textContent = 'Lỗi: ' + error.message;
        message.className = 'error';
      } finally {
        btn.disabled = false;
        spinner.style.display = 'none';
      }
    });
    });
  </script>

  </div>

  <div id="hoadon" class="page">
  <h1>Thêm Hóa đơn mới</h1>
  <form id="hoadonForm">
    <input type="number" id="maKhachHangHD" placeholder="Mã khách hàng" required><br>
    <input type="datetime-local" id="ngay" required><br>
    <button type="submit" id="submitHoaDonBtn">Thêm <div class="spinner" id="spinnerHoaDon"></div></button>
  </form>
  <p id="messageHoaDon"></p>

  <h1>Danh sách Hóa đơn</h1>
  <div id="hoadonList"></div>

  <h1>Tra cứu chi tiết hóa đơn</h1>
  <form id="tracuuForm">
    <input type="number" id="idTracuu" placeholder="Nhập ID hóa đơn" required><br>
    <button type="submit">Xem</button>
  </form>
  <div id="chitietResult"></div>
  </div>

  <div id="chitiethoadon" class="page">
  <h1>Xem Chi tiết Hóa đơn</h1>
  <form id="xemChiTietForm">
    <input type="number" id="maHoaDonXem" placeholder="Mã hóa đơn" required><br>
    <button type="submit">Xem</button>
  </form>
  <div id="chitietResultXem"></div>

  <h1>Thêm Dòng Sản phẩm vào Hóa đơn</h1>
  <form id="themChiTietForm">
    <input type="number" id="maHoaDonThem" placeholder="Mã hóa đơn" required><br>
    <input type="number" id="maSanPhamThem" placeholder="Mã sản phẩm" required><br>
    <input type="number" id="soLuongThem" placeholder="Số lượng" required><br>
    <button type="submit" id="submitChiTietBtn">Thêm <div class="spinner" id="spinnerChiTiet"></div></button>
  </form>
  <p id="messageChiTiet"></p>
  </div>

  <div id="khohang" class="page">
  <h1>Danh sách Kho hàng</h1>
  <div id="khohangList"></div>
  </div>
</body>
</html>