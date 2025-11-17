<?php
function sendJsonResponse(array $data) {
  header('Content-Type: application/json');
  echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  exit;
}

function getPDOForSite(string $site): PDO {
  $dsn = sprintf(
    'sqlsrv:Server=%s,%s;Database=%s;TrustServerCertificate=1',
    getenv("DB_{$site}_HOST"),
    getenv("DB_{$site}_PORT"),
    getenv("DB_{$site}_NAME")
  );
  return new PDO($dsn, getenv("DB_{$site}_USER"), getenv("DB_{$site}_PASS"));
}

/**
 * Xác định site (A hoặc B) dựa trên quy tắc phân mảnh cho Khách hàng và Sản phẩm.
 * @param string $entity 'KhachHang', 'SanPham', or 'HoaDon'
 * @param int $id ID cần kiểm tra
 * @return string 'A' hoặc 'B'
 * @throws Exception
 */
function determineSite(string $entity, int $id): string {
  switch ($entity) {
    case 'KhachHang':
      // A: 10001–15000, B: 15001–20000
      return ($id >= 10001 && $id <= 15000) ? 'A' : 'B';
    case 'SanPham':
      // A: 10001–60000, B: 60001–110000
      return ($id >= 10001 && $id <= 60000) ? 'A' : 'B';
    default:
      throw new Exception("Entity not supported for site determination.");
  }
}

function generateUniqueMaHoaDon(PDO $conn, string $siteId): int {

  $initialSeed = ($siteId === 'A') ? 10001 : 110001;

  $stmt = $conn->prepare("SELECT MAX(MaHoaDon) AS max_id FROM Shop.dbo.HoaDon");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $maxId = $result['max_id'];

  if ($maxId === null) {
    return $initialSeed;
  } else {
    $newId = $maxId + 1;

    $upperBound = ($siteId === 'A') ? 110000 : 210000;
    if ($newId > $upperBound) {
      throw new Exception("Lỗi: Site {$siteId} đã hết MaHoaDon trong dải cho phép.");
    }

    return $newId;
  }
}

function getRandomKhachHang() {
  $site = (rand(0, 1) === 0) ? 'A' : 'B';
  $conn = getPDOForSite("SITE_{$site}");

  $stmt = $conn->prepare("
        SELECT TOP 1 MaKhachHang, TenKh
        FROM KhachHang 
        ORDER BY NEWID()
    ");
  $stmt->execute();
  $kh = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$kh) {
    throw new Exception("Lỗi: Không thể lấy MaKhachHang ngẫu nhiên từ Site {$site}.");
  }

  return $kh;
}
function getRandomSanPham(): array {
  $site = (rand(0, 1) === 0) ? 'A' : 'B';
  $conn = getPDOForSite("SITE_{$site}");

  $stmt = $conn->prepare("
        SELECT TOP 1 MaSanPham, TenSanPham, TonKho
        FROM SanPham
        ORDER BY NEWID()
    ");
  $stmt->execute();
  $sp = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$sp) {
    throw new Exception("Lỗi: Không thể lấy MaSanPham ngẫu nhiên từ Site {$site}.");
  }

  return $sp;
}