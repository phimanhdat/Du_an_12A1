<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$file = __DIR__ . "/rsvp.json";

// Nhận dữ liệu
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Lấy dữ liệu
$name   = trim($data["name"] ?? "");
$status = trim($data["status"] ?? ""); // yes / no / maybe
$guest  = intval($data["guest"] ?? 1);
$message = trim($data["message"] ?? "");

// Validate
if ($name === "" || !in_array($status, ["yes", "no", "maybe"])) {
    echo json_encode([
        "success" => false,
        "msg" => "Dữ liệu không hợp lệ"
    ]);
    exit;
}

// Đọc dữ liệu cũ
if (file_exists($file)) {
    $json = file_get_contents($file);
    $rsvps = json_decode($json, true);
    if (!is_array($rsvps)) $rsvps = [];
} else {
    $rsvps = [];
}

// Ghi RSVP mới
$rsvps[] = [
    "name"    => htmlspecialchars($name),
    "status"  => $status, // yes | no | maybe
    "guest"   => $guest,
    "message" => htmlspecialchars($message),
    "time"    => date("Y-m-d H:i:s")
];

// Lưu file
file_put_contents(
    $file,
    json_encode($rsvps, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
);

echo json_encode([
    "success" => true,
    "msg" => "Đã ghi nhận xác nhận tham dự"
]);
