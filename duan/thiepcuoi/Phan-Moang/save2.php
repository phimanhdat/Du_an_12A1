<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

$input = file_get_contents("php://input");
$newData = json_decode($input, true);

if ($newData && !empty($newData['name'])) {
    $filePath = 'data2.json';
    $currentData = [];
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $currentData = json_decode($content, true) ?? [];
    }
    $newData['time'] = date('Y-m-d H:i:s');
    $currentData[] = $newData;
    if (file_put_contents($filePath, json_encode($currentData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi ghi file. Hãy CHMOD data2.json thành 666"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ"]);
}
?>