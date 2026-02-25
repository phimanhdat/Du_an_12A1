<?php
$data = [];

if (file_exists("rsvp.json")) {
    $data = json_decode(file_get_contents("rsvp.json"), true);
}
?>

<h2>Danh sách người phản hồi</h2>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Họ Tên</th>
        <th>Email</th>
        <th>Trạng thái</th>
        <th>Khách của</th>
        <th>Số lượng</th>
        <th>Sự kiện</th>
        <th>Thời gian</th>
    </tr>

    <?php foreach ($data as $item): ?>
    <tr>
        <td><?= $item["name"] ?></td>
        <td><?= $item["email"] ?></td>
        <td><?= $item["status"] ?></td>
        <td><?= $item["guest_of"] ?></td>
        <td><?= $item["amount"] ?></td>
        <td><?= $item["event"] ?></td>
        <td><?= $item["time"] ?></td>
    </tr>
    <?php endforeach; ?>
</table>
