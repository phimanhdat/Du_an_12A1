<?php
/**
 * API: LẤY DANH SÁCH LỜI CHÚC
 * Trả về JSON array
 */

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-cache, no-store, must-revalidate");

$file = __DIR__ . "/loichuc.json";

// Nếu file chưa tồn tại → trả mảng rỗng
if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

// Đọc file
$json = file_get_contents($file);
$data = json_decode($json, true);

// Nếu dữ liệu lỗi → trả mảng rỗng
if (!is_array($data)) {
    echo json_encode([]);
    exit;
}

/**
 * (Tuỳ chọn) Sắp xếp lời chúc mới nhất lên trước
 * Bỏ comment nếu bạn muốn
 */
// usort($data, function ($a, $b) {
//     return strtotime($b["time"] ?? 0) - strtotime($a["time"] ?? 0);
// });

/**
 * (Tuỳ chọn) Giới hạn số lời chúc trả về
 * Ví dụ: 50 lời chúc gần nhất
 */
// $data = array_slice($data, 0, 50);

// Trả dữ liệu
echo json_encode(
    $data,
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);
