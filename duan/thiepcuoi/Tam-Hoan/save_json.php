<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

$data = [
    'time' => date('Y-m-d H:i:s'),
    'name' => $_POST['name'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'tham_gia' => $_POST['form_item7'] ?? '',
    'vai_tro' => $_POST['form_item8'] ?? [],
    'tiec' => $_POST['form_item9'] ?? '',
    'so_nguoi' => $_POST['form_item381'] ?? '',
    'loi_chuc' => $_POST['message'] ?? ''
];

$file = 'data.json';

/* Nếu file chưa tồn tại → tạo mảng rỗng */
if (!file_exists($file)) {
    file_put_contents($file, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/* Đọc dữ liệu cũ */
$oldData = json_decode(file_get_contents($file), true);

/* Thêm dữ liệu mới */
$oldData[] = $data;

/* Ghi lại file */
file_put_contents(
    $file,
    json_encode($oldData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

/* Chuyển hướng sau khi gửi */
header("Location: thankyou.html");
exit;
