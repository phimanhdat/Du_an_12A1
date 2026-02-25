<?php
session_start();

// --- CẤU HÌNH ---
$PASSWORD_ADMIN = '123456'; // Đổi mật khẩu admin tại đây
$SOURCE_FILES = ['index.php', 'rsvp.html', 'save_rsvp.php']; // Các file cần copy

// --- XỬ LÝ LOGIN/LOGOUT ---
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: admin.php");
    exit;
}

if (isset($_POST['login_pass'])) {
    if ($_POST['login_pass'] === $PASSWORD_ADMIN) {
        $_SESSION['is_admin'] = true;
    } else {
        $error = "Mật khẩu sai!";
    }
}

// Chặn truy cập nếu chưa login
if (!isset($_SESSION['is_admin'])) {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Đăng nhập Admin</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&display=swap" rel="stylesheet">
        <style>
            body { background: #020617; color: #fff; display: flex; height: 100vh; justify-content: center; align-items: center; font-family: sans-serif; }
            .login-box { background: rgba(255,255,255,0.05); border: 1px solid #d4af37; padding: 40px; border-radius: 20px; text-align: center; }
            input { background: rgba(0,0,0,0.5); border: 1px solid #555; padding: 10px; color: #fff; width: 100%; margin-bottom: 15px; border-radius: 5px; }
            button { background: linear-gradient(45deg, #b38728, #fcf6ba, #d4af37); color: #000; font-weight: bold; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-family: 'Cinzel', serif;}
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2 class="text-2xl text-[#d4af37] font-bold mb-4 uppercase" style="font-family: 'Cinzel'">Admin Panel</h2>
            <?php if(isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>
            <form method="POST">
                <input type="password" name="login_pass" placeholder="Nhập mật khẩu..." required>
                <button type="submit">Truy cập</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// --- HÀM HỖ TRỢ ---
function slugify($str) {
    $str = trim(mb_strtolower($str));
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/([\s]+)/', '-', $str);
    return $str;
}

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) { return; }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') { $dirPath .= '/'; }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) { deleteDir($file); } else { unlink($file); }
    }
    rmdir($dirPath);
}

// --- XỬ LÝ TẠO LINK (CREATE) ---
if (isset($_POST['create_name'])) {
    $guestName = trim($_POST['create_name']);
    $folderName = slugify($guestName);

    if (empty($folderName)) {
        $msg = ['type' => 'error', 'text' => 'Tên không hợp lệ!'];
    } elseif (is_dir($folderName)) {
        $msg = ['type' => 'error', 'text' => 'Link cho khách này đã tồn tại!'];
    } else {
        // 1. Tạo thư mục
        mkdir($folderName);
        
        // 2. Copy các file cần thiết
        foreach ($SOURCE_FILES as $file) {
            if (file_exists($file)) {
                if ($file == 'index.php') {
                    // Xử lý riêng file index.php để thay tên khách
                    $content = file_get_contents($file);
                    // Tìm dòng $khach = "..."; và thay thế
                    // Regex này tìm biến $khach gán giá trị bất kỳ trong dấu ngoặc kép
                    $newContent = preg_replace('/\$khach\s*=\s*".*?";/', '$khach = "'.$guestName.'";', $content);
                    file_put_contents($folderName . '/' . $file, $newContent);
                } else {
                    copy($file, $folderName . '/' . $file);
                }
            }
        }
        
        // 3. Tạo file data.json rỗng cho folder con
        file_put_contents($folderName . '/data.json', '[]');

        $msg = ['type' => 'success', 'text' => "Đã tạo link cho: $guestName"];
    }
}

// --- XỬ LÝ XÓA LINK (DELETE) ---
if (isset($_POST['delete_folder'])) {
    $folderToDelete = $_POST['delete_folder'];
    if (is_dir($folderToDelete) && !in_array($folderToDelete, ['.', '..', 'css', 'js', 'images'])) {
        deleteDir($folderToDelete);
        $msg = ['type' => 'success', 'text' => 'Đã xóa link thành công!'];
    }
}

// --- LẤY DANH SÁCH CÁC FOLDER ---
$folders = array_filter(glob('*'), 'is_dir');
$guestLinks = [];
foreach ($folders as $folder) {
    // Chỉ lấy folder nào có file index.php bên trong (để tránh lấy folder rác)
    if (file_exists($folder . '/index.php')) {
        // Đọc thử tên khách trong file index.php
        $content = file_get_contents($folder . '/index.php');
        preg_match('/\$khach\s*=\s*"(.*?)";/', $content, $matches);
        $name = isset($matches[1]) ? $matches[1] : $folder;
        $guestLinks[] = ['folder' => $folder, 'name' => $name];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý link mời</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { background: #0f172a; color: #fff; font-family: 'Quicksand', sans-serif; }
        .font-royal { font-family: 'Cinzel', serif; }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 175, 55, 0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .btn-gold {
            background: linear-gradient(45deg, #b38728, #fcf6ba, #d4af37);
            color: #000; font-weight: bold; transition: all 0.3s;
        }
        .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4); }
        .text-gold { color: #d4af37; }
        
        /* SweetAlert Dark Override */
        div:where(.swal2-container) div:where(.swal2-popup) { background: #1e293b !important; color: #fff !important; border: 1px solid #d4af37; }
    </style>
</head>
<body class="p-6">

    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-royal text-gold font-bold">Admin Dashboard</h1>
                <p class="text-gray-400 text-sm">Quản lý Link Mời</p>
            </div>
            <a href="?action=logout" class="text-red-400 hover:text-red-300 text-sm font-bold"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </div>

        <div class="glass-panel rounded-2xl p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 text-gold"><i class="fas fa-magic mr-2"></i>Tạo Link Mới</h2>
            <form method="POST" class="flex gap-4">
                <input type="text" name="create_name" placeholder="Nhập tên khách mời (VD: Nguyễn Văn A)" class="flex-1 bg-black/30 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-[#d4af37] transition text-white" required>
                <button type="submit" class="btn-gold px-6 py-3 rounded-lg font-royal uppercase tracking-wider">
                    <i class="fas fa-plus-circle mr-2"></i> Tạo Link
                </button>
            </form>
        </div>

        <div class="glass-panel rounded-2xl p-6">
            <h2 class="text-xl font-bold mb-6 text-gold flex items-center justify-between">
                <span><i class="fas fa-list mr-2"></i>Danh sách đã tạo (<?php echo count($guestLinks); ?>)</span>
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-700 text-sm uppercase">
                            <th class="py-3 px-4">Tên Khách</th>
                            <th class="py-3 px-4">Đường Dẫn (Folder)</th>
                            <th class="py-3 px-4 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-200">
                        <?php if (empty($guestLinks)): ?>
                            <tr><td colspan="3" class="text-center py-6 text-gray-500">Chưa có link nào được tạo.</td></tr>
                        <?php else: ?>
                            <?php foreach ($guestLinks as $link): ?>
                                <?php 
                                    $fullUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/" . $link['folder'];
                                ?>
                                <tr class="border-b border-gray-700/50 hover:bg-white/5 transition">
                                    <td class="py-4 px-4 font-bold text-lg text-gold"><?php echo $link['name']; ?></td>
                                    <td class="py-4 px-4 text-sm text-gray-400 font-mono">/<?php echo $link['folder']; ?></td>
                                    <td class="py-4 px-4 flex gap-2 justify-center">
                                        <button onclick="copyLink('<?php echo $fullUrl; ?>')" class="bg-blue-600/20 text-blue-400 hover:bg-blue-600 hover:text-white px-3 py-2 rounded-lg transition border border-blue-600/50" title="Sao chép">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <a href="<?php echo $link['folder']; ?>" target="_blank" class="bg-green-600/20 text-green-400 hover:bg-green-600 hover:text-white px-3 py-2 rounded-lg transition border border-green-600/50" title="Mở trang">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <button onclick="confirmDelete('<?php echo $link['folder']; ?>', '<?php echo $link['name']; ?>')" class="bg-red-600/20 text-red-400 hover:bg-red-600 hover:text-white px-3 py-2 rounded-lg transition border border-red-600/50" title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display:none;">
        <input type="hidden" name="delete_folder" id="deleteInput">
    </form>

    <script>
        // Thông báo PHP
        <?php if(isset($msg)): ?>
            Swal.fire({
                icon: '<?php echo $msg['type']; ?>',
                title: '<?php echo $msg['type'] == "success" ? "Thành công" : "Lỗi"; ?>',
                text: '<?php echo $msg['text']; ?>',
                timer: 2000,
                showConfirmButton: false
            });
        <?php endif; ?>

        // Copy Link
        function copyLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                const Toast = Swal.mixin({
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
                    timerProgressBar: true, background: '#1e293b', color: '#fff'
                });
                Toast.fire({ icon: 'success', title: 'Đã sao chép link!' });
            });
        }

        // Xóa Link
        function confirmDelete(folder, name) {
            Swal.fire({
                title: 'Xóa link của ' + name + '?',
                text: "Bạn không thể hoàn tác hành động này!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Vâng, xóa đi!',
                cancelButtonText: 'Hủy',
                background: '#1e293b', color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteInput').value = folder;
                    document.getElementById('deleteForm').submit();
                }
            })
        }
    </script>
</body>
</html>