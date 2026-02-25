<?php
header("Content-Type: application/json; charset=UTF-8");

$data = [
    "name"    => $_POST["name"],
    "email"   => $_POST["email"],
    "status"  => $_POST["status"],
    "guest_of"=> $_POST["guest_of"],
    "amount"  => $_POST["amount"],
    "event"   => $_POST["event"],
    "time"    => date("Y-m-d H:i:s")
];

// File lưu
$file = "rsvp.json";

// Nếu file đã có dữ liệu thì đọc
if (file_exists($file)) {
    $list = json_decode(file_get_contents($file), true);
} else {
    $list = [];
}

// Thêm dữ liệu mới
$list[] = $data;

// Ghi ngược lại file
file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(["success" => true, "message" => "Phản hồi đã được gửi!"]);
?>
