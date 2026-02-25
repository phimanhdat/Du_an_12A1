<?php
session_start();

// --- CẤU HÌNH HỆ THỐNG ---
$PASSWORD_ADMIN = '123456'; 
$CONFIG_FILE = 'config.php';
$SOURCE_FILES = ['index.php', 'config.php']; 
$SOURCE_FOLDERS = ['th','user']; 

// --- 1. KIỂM TRA ĐĂNG NHẬP ---
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: admin.php");
    exit;
}
if (isset($_POST['login_pass']) && $_POST['login_pass'] === $PASSWORD_ADMIN) {
    $_SESSION['is_admin'] = true;
}
if (!isset($_SESSION['is_admin'])) {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8"><title>Admin Login</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>body { background: #020617; display: flex; height: 100vh; justify-content: center; align-items: center; color: white; font-family: sans-serif; }</style>
    </head>
    <body>
        <form method="POST" class="border border-yellow-600/50 p-10 rounded-2xl bg-slate-900 text-center shadow-2xl">
            <h2 class="mb-6 text-2xl font-bold text-yellow-500 uppercase tracking-widest">Admin Panel</h2>
            <input type="password" name="login_pass" class="bg-black border border-gray-700 p-3 mb-4 block w-full rounded-lg text-center" placeholder="Nhập mật khẩu">
            <button class="bg-yellow-600 hover:bg-yellow-500 w-full py-3 rounded-lg font-bold text-black transition-all">ĐĂNG NHẬP</button>
        </form>
    </body>
    </html>
    <?php exit;
}

// --- 2. CÁC HÀM HỖ TRỢ ---
function recurse_copy($src, $dst) {
    if (!is_dir($src)) return;
    @mkdir($dst);
    $dir = opendir($src);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) recurse_copy($src . '/' . $file, $dst . '/' . $file);
            else copy($src . '/' . $file, $dst . '/' . $file);
        }
    }
    closedir($dir);
}

function deleteDir($dirPath) {
    if (!is_dir($dirPath)) return;
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) deleteDir($file); else unlink($file);
    }
    @rmdir($dirPath);
}

function slugify($str) {
    $str = trim(mb_strtolower($str));
    $search = ['à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ','è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ','ì','í','ị','ỉ','ĩ','ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ','ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ','ỳ','ý','ỵ','ỷ','ỹ','đ'];
    $replace = ['a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','e','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y','d'];
    $str = str_replace($search, $replace, $str);
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/([\s]+)/', '-', $str);
    return $str;
}

// --- 3. XỬ LÝ DỮ LIỆU ---
include $CONFIG_FILE; // Lấy $gio và $khach hiện tại

// Cập nhật cấu hình chung
if (isset($_POST['update_config'])) {
    $gio = $_POST['config_gio'];
    $khach = $_POST['config_khach'];
    $content = "<?php\n\$gio = \"$gio\";\n\$khach = \"$khach\";\n?>";
    file_put_contents($CONFIG_FILE, $content);
    $msg = ['type' => 'success', 'text' => 'Đã cập nhật cấu hình gốc!'];
}

// Tạo link mới
if (isset($_POST['create_name'])) {
    $guestName  = trim($_POST['create_name']);
    $folderName = slugify($guestName);

    if (!empty($folderName) && !is_dir($folderName)) {

        // 1️⃣ Tạo folder khách
        mkdir($folderName, 0755, true);

        // 2️⃣ Copy folder ảnh
        // 2️⃣ Copy folder th + user
foreach ($SOURCE_FOLDERS as $src) {
    if (is_dir($src)) {
        recurse_copy($src, $folderName . '/' . $src);
    }
}


        // 3️⃣ Copy các file nguồn (bao gồm config.php GỐC)
        foreach ($SOURCE_FILES as $file) {
            if (file_exists($file)) {
                copy($file, $folderName . '/' . $file);
            }
        }

        // 4️⃣ TÍNH URL RIÊNG CHO KHÁCH
        $guestUrl = rtrim($url, '/') . '/' . $folderName . '/';

        // 5️⃣ GHI ĐÈ config.php TRONG FOLDER KHÁCH
        $guestConfigPath = $folderName . '/config.php';

        $newConfigContent = "<?php
\$gio = \"$gio\";
\$khach = \"$guestName\";
\$url = \"$guestUrl\";
?>";

        file_put_contents($guestConfigPath, $newConfigContent);

        $msg = ['type' => 'success', 'text' => "Đã tạo link cho khách: $guestName"];
    } else {
        $msg = ['type' => 'error', 'text' => "Tên không hợp lệ hoặc link đã tồn tại!"];
    }
}


// Xóa link
if (isset($_POST['delete_folder'])) {
    $delFolder = $_POST['delete_folder'];
    if (is_dir($delFolder) && !in_array($delFolder, ['.', '..', $SOURCE_FOLDER])) {
        deleteDir($delFolder);
        $msg = ['type' => 'success', 'text' => 'Đã xóa thư mục khách thành công!'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Link Kỷ Yếu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #0f172a; color: #fff; font-family: sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(212, 175, 55, 0.2); }
        .btn-gold { background: linear-gradient(45deg, #b38728, #fcf6ba, #d4af37); color: #000; font-weight: bold; }
    </style>
</head>
<body class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-yellow-500 uppercase tracking-tight">Admin Dashboard</h1>
            <a href="?action=logout" class="text-red-400 hover:text-red-300 transition-colors"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </div>

        <div class="glass rounded-2xl p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 text-yellow-500 italic border-b border-gray-700 pb-2"><i class="fas fa-tools mr-2"></i>Cấu hình gốc</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Giờ tổ chức:</label>
                    <input type="text" name="config_gio" value="<?php echo $gio; ?>" class="w-full bg-black/40 border border-gray-600 p-2 rounded-lg text-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Lời chào mặc định:</label>
                    <input type="text" name="config_khach" value="<?php echo $khach; ?>" class="w-full bg-black/40 border border-gray-600 p-2 rounded-lg text-white">
                </div>
                <div class="flex items-end">
                    <button type="submit" name="update_config" class="btn-gold w-full py-2 rounded-lg uppercase text-sm shadow-lg">Lưu cấu hình</button>
                </div>
            </form>
        </div>

        <div class="glass rounded-2xl p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 text-yellow-500 italic border-b border-gray-700 pb-2"><i class="fas fa-user-plus mr-2"></i>Tạo link khách hàng</h2>
            <form method="POST" class="flex gap-4">
                <input type="text" name="create_name" placeholder="Nhập tên khách mời..." class="flex-1 bg-black/40 border border-gray-600 rounded-lg px-4 py-2 text-white outline-none focus:border-yellow-500 transition-all" required>
                <button type="submit" class="btn-gold px-8 py-2 rounded-lg uppercase shadow-lg">Tạo Link</button>
            </form>
            <p class="mt-2 text-[10px] text-gray-500">* Hệ thống sẽ tự động cập nhật tên vào file config và copy folder ảnh.</p>
        </div>

        <div class="glass rounded-2xl p-6">
            <h2 class="text-xl font-bold mb-6 text-yellow-500 italic border-b border-gray-700 pb-2"><i class="fas fa-list mr-2"></i>Danh sách Link đã tạo</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-700 text-xs uppercase">
                            <th class="py-3 px-2">Khách mời</th>
                            <th class="py-3 px-2 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dirs = array_filter(glob('*'), 'is_dir');
                        foreach ($dirs as $d):
                            if(!file_exists($d.'/index.php')) continue;
                            $actualUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . $d . "/";
                        ?>
                        <tr class="border-b border-gray-800/50 hover:bg-white/5 transition-all">
                            <td class="py-4 px-2">
                                <div class="font-bold text-yellow-100"><?php echo strtoupper($d); ?></div>
                                <div class="text-[10px] text-gray-500 italic"><?php echo $actualUrl; ?></div>
                            </td>
                            <td class="py-4 px-2 text-right">
                                <button onclick="copyLink('<?php echo $actualUrl; ?>')" class="bg-blue-600/20 text-blue-400 p-2 rounded-md border border-blue-600/30 hover:bg-blue-600 hover:text-white transition-all mr-2" title="Sao chép Link">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <a href="<?php echo $d; ?>/" target="_blank" class="bg-green-600/20 text-green-400 p-2 rounded-md border border-green-600/30 hover:bg-green-600 hover:text-white transition-all mr-2">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <button onclick="confirmDel('<?php echo $d; ?>')" class="bg-red-600/20 text-red-400 p-2 rounded-md border border-red-600/30 hover:bg-red-600 hover:text-white transition-all">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <form id="delForm" method="POST" style="display:none;"><input type="hidden" name="delete_folder" id="delInput"></form>

    <script>
        function copyLink(text) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({ icon: 'success', title: 'Đã copy!', timer: 800, showConfirmButton: false, background: '#1e293b', color: '#fff' });
            });
        }
        function confirmDel(folder) {
            Swal.fire({
                title: 'Xóa link này?',
                text: "Toàn bộ dữ liệu của " + folder + " sẽ bị xóa sạch!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Xóa ngay',
                background: '#1e293b', color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delInput').value = folder;
                    document.getElementById('delForm').submit();
                }
            })
        }
        <?php if(isset($msg)): ?>
            Swal.fire({ icon: '<?php echo $msg['type']; ?>', title: 'Thông báo', text: '<?php echo $msg['text']; ?>', timer: 2000, background: '#1e293b', color: '#fff' });
        <?php endif; ?>
    </script>
</body>
</html>