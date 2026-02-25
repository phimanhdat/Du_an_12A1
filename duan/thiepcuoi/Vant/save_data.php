<?php
// save_data.php

// Cho phép nhận dữ liệu từ tên miền khác (nếu cần)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Lấy dữ liệu gửi lên từ JavaScript
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data) {
    // Tên file muốn lưu
    $file = 'save.json';

    // Đọc dữ liệu cũ nếu có
    $current_data = [];
    if (file_exists($file)) {
        $json_content = file_get_contents($file);
        $current_data = json_decode($json_content, true) ?? [];
    }

    // Thêm thời gian tạo
    $data['timestamp'] = date('Y-m-d H:i:s');
    
    // Thêm dữ liệu mới vào mảng
    $current_data[] = $data;

    // Ghi lại vào file (dưới dạng JSON)
    if (file_put_contents($file, json_encode($current_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        echo json_encode(["status" => "success", "message" => "Đã lưu thành công"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Không thể ghi file"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Không có dữ liệu"]);
}
?>