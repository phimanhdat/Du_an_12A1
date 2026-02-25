<?php
header("Content-Type: application/json; charset=UTF-8");

// Lấy dữ liệu từ AJAX
$name = $_POST["name"] ?? "";
$wish = $_POST["wish"] ?? "";

if (!$name || !$wish) {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
    exit;
}

$data = [
    "name" => $name,
    "wish" => $wish,
    "time" => date("Y-m-d H:i:s")
];

// Tên file JSON để lưu
$file = "wish_list.json";

// Nếu file đã có → đọc
if (file_exists($file)) {
    $list = json_decode(file_get_contents($file), true);
} else {
    $list = [];
}

// Thêm lời chúc mới
array_unshift($list, $data); // thêm vào đầu cho mới nhất đứng trên

// Ghi trở lại file
file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(["success" => true]);
?>
