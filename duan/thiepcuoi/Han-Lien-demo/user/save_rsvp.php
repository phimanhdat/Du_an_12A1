<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$file = __DIR__ . "/rsvp.json";

// Nhận dữ liệu
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Lấy dữ liệu (Mapping lại tên biến từ Frontend cho khớp)
$name = trim($data["name"] ?? "");

// Frontend gửi "is_attending", ta lấy nó gán vào biến status
$status = trim($data["is_attending"] ?? ""); 

// Frontend gửi "guest_count", ta lấy nó gán vào guest
// Xử lý chuỗi "1 Người" -> lấy số 1
$guest_raw = $data["guest_count"] ?? "1"; 
$guest = intval($guest_raw); 

$message = trim($data["message"] ?? "");

// Validate đơn giản hơn (Chỉ cần có tên và trạng thái)
if ($name === "" || $status === "") {
    echo json_encode([
        "success" => false,
        "msg" => "Dữ liệu không hợp lệ (Thiếu tên hoặc trạng thái)"
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
    "status"  => htmlspecialchars($status), // Lưu nguyên văn câu tiếng Việt
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
?>