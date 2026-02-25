<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Đường dẫn file JSON
$file = __DIR__ . "/loichuc.json";

// Nhận dữ liệu từ frontend
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Validate dữ liệu
$name = trim($data["name"] ?? "");
// Sửa "message" thành "wish_message" để khớp với input từ Javascript
$message = trim($data["wish_message"] ?? "");

if ($name === "" || $message === "") {
    echo json_encode([
        "success" => false,
        "msg" => "Thiếu tên hoặc lời chúc"
    ]);
    exit;
}

// Đọc dữ liệu cũ
if (file_exists($file)) {
    $json = file_get_contents($file);
    $wishes = json_decode($json, true);
    if (!is_array($wishes)) $wishes = [];
} else {
    $wishes = [];
}

// Thêm lời chúc mới
$wishes[] = [
    "name" => htmlspecialchars($name),
    "message" => htmlspecialchars($message),
    "time" => date("Y-m-d H:i:s")
];

// Ghi lại file
file_put_contents(
    $file,
    json_encode($wishes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
);

// Trả kết quả
echo json_encode([
    "success" => true,
    "msg" => "Đã lưu lời chúc"
]);
