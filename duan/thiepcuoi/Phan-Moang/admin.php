<?php
// Cấu hình các file cần coppy (loại trừ các file không cần thiết)
$filesToCopy = ['index.php', 'save.php', 'save2.php', 'data.json', 'data2.json'];

// Hàm tạo slug (manh-dat) từ tên tiếng Việt
function create_slug($string) {
    $search = array(
        '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
        '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
        '#(ì|í|ị|ỉ|ĩ)#',
        '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
        '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
        '#(ỳ|ý|ỵ|ỷ|ỹ)#',
        '#(đ)#',
        '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
        '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
        '#(Ì|Í|Ị|Ỉ|Ĩ)#',
        '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
        '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
        '#(Ỳ|Ý|Ị|Ỷ|Ỹ)#',
        '#(Đ)#',
        '/[^a-zA-Z0-9\-\_]/',
    );
    $replace = array('a', 'e', 'i', 'o', 'u', 'y', 'd', 'A', 'E', 'I', 'O', 'U', 'Y', 'D', '-',);
    $string = preg_replace($search, $replace, $string);
    $string = preg_replace('/(-)+/', '-', $string);
    return strtolower(trim($string, '-'));
}

// Xử lý Xóa folder
if (isset($_GET['delete'])) {
    $dir = $_GET['delete'];
    if (is_dir($dir) && $dir != '.' && $dir != '..') {
        system("rm -rf " . escapeshellarg($dir)); // Xóa toàn bộ folder trên Linux hosting
    }
    header("Location: admin.php");
}

// Xử lý Tạo Link
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guest_name'])) {
    $name = $_POST['guest_name'];
    $slug = create_slug($name);

    if (!is_dir($slug)) {
        mkdir($slug, 0777, true);
        
        // Coppy file
        foreach ($filesToCopy as $file) {
            if (file_exists($file)) copy($file, $slug . '/' . $file);
        }

        // Sửa biến $khach trong index.php của folder mới
        $indexPath = $slug . '/index.php';
        $content = file_get_contents($indexPath);
        $content = preg_replace('/\$khach\s*=\s*".*?";/', '$khach = "' . $name . '";', $content);
        file_put_contents($indexPath, $content);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quản Lý Link Mời Đám Cưới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-color: #d63384; --secondary-color: #6c757d; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background-color: white !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .btn-primary { background-color: var(--primary-color); border: none; }
        .btn-primary:hover { background-color: #bc2a71; }
        .table thead { background-color: var(--primary-color); color: white; }
        .link-text { max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block; vertical-align: middle; }
    </style>
</head>
<body>

<nav class="navbar navbar-light mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1 text-primary"><i class="fas fa-heart"></i> Wedding Admin</span>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card p-4">
                <h5 class="card-title mb-4">Tạo Link Mới</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Tên khách mời</label>
                        <input type="text" name="guest_name" class="form-control" placeholder="VD: Anh Mạnh Đạt" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-plus-circle"></i> Tạo Link Ngay
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5 class="card-title mb-4">Danh Sách Đã Tạo</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Khách mời</th>
                                <th>Liên kết</th>
                                <th class="text-center">Quản lý</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $dirs = array_filter(glob('*'), 'is_dir');
                            foreach ($dirs as $dir) {
                                $fullLink = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" . $dir . "/index.php";
                                echo "<tr>
                                    <td><strong>$dir</strong></td>
                                    <td>
                                        <span class='link-text text-muted'>$fullLink</span>
                                        <button class='btn btn-sm btn-outline-primary ms-2' onclick=\"copyLink('$fullLink')\"><i class='fas fa-copy'></i></button>
                                    </td>
                                    <td class='text-center'>
                                        <a href='$dir/user1.html' class='btn btn-sm btn-info text-white' title='Xem xác nhận'><i class='fas fa-user-check'></i></a>
                                        <a href='$dir/user2.html' class='btn btn-sm btn-warning text-white' title='Xem lời chúc'><i class='fas fa-comment-dots'></i></a>
                                        <a href='?delete=$dir' class='btn btn-sm btn-danger' onclick='return confirm(\"Xóa toàn bộ folder $dir?\")'><i class='fas fa-trash'></i></a>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyLink(text) {
    navigator.clipboard.writeText(text);
    alert('Đã sao chép đường dẫn khách mời!');
}
</script>
</body>
</html>