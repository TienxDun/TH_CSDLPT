<?php
function callBuyAPI(int $maKhachHang, array $orders)
{
  $payload = [
    'maKhachHang' => $maKhachHang,
    'orders' => $orders
  ];

  $options = [
    'http' => [
      'method' => 'POST',
      'header' => "Content-Type: application/json\r\n",
      'content' => json_encode($payload)
    ]
  ];

  $apiUrl = 'http://api_php_123456:8080/api/transactions/buy';

  return file_get_contents($apiUrl, false, stream_context_create($options));
}

function GetKH()
{
  $options = [
    'http' => [
      'method' => 'GET',
    ]
  ];

  $apiUrl = 'http://api_php_123456:8080/api/KhachHang/random';

  $result = file_get_contents($apiUrl, false, stream_context_create($options));
  $data = json_decode($result, 1);
  if ($data['MaKhachHang']??0)
    return $data;
  throw new Exception($result);
}

function GetSP()
{
  $options = [
    'http' => [
      'method' => 'GET',
    ]
  ];

  $apiUrl = 'http://api_php_123456:8080/api/SanPham/random';

  $result = file_get_contents($apiUrl, false, stream_context_create($options));
  $data = json_decode($result, 1);
  if ($data['MaSanPham']??0)
    return $data;
  throw new Exception($result);
}