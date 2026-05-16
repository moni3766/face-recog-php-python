<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$msg = "";
$db_file = __DIR__ . '/database/employees.json';

// --- CHỨC NĂNG XỬ LÝ XÓA NHÂN VIÊN ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delete_id = trim($_GET['id']);
    
    if (file_exists($db_file)) {
        $employees = json_decode(file_get_contents($db_file), true) ?? [];
        $new_employees = [];
        
        foreach ($employees as $emp) {
            if ($emp['id'] === $delete_id) {
                // 1. Tìm và xóa file ảnh tương ứng trong thư mục dataset
                $image_extensions = ['.jpg', '.jpeg', '.png'];
                foreach ($image_extensions as $ext) {
                    $file_path = __DIR__ . '/dataset/' . $delete_id . $ext;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            } else {
                // Giữ lại các nhân viên không bị xóa
                $new_employees[] = $emp;
            }
        }
        
        // 2. Lưu lại danh sách mới vào file JSON
        file_put_contents($db_file, json_encode($new_employees, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        header("Location: admin_dashboard.php?msg=deleted");
        exit;
    }
}

// Hiển thị thông báo sau khi chuyển hướng trang xóa
if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
    $msg = "<p style='color: orange; font-weight: bold;'>➔ Đã xóa nhân viên và tệp tin ảnh gốc thành công!</p>";
}

// --- CHỨC NĂNG ĐĂNG KÝ / CẬP NHẬT NHÂN VIÊN ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
    $emp_id = trim($_POST['emp_id']);
    $emp_name = trim($_POST['emp_name']);
    $emp_dept = trim($_POST['emp_dept']);
    $image_data = $_POST['image_base64']; 

    if (!empty($emp_id) && !empty($emp_name) && !empty($image_data)) {
        $image_data = str_replace('data:image/jpeg;base64,', '', $image_data);
        $image_data = str_replace(' ', '+', $image_data);
        $file_data = base64_decode($image_data);
        
        $dataset_dir = __DIR__ . '/dataset/';
        
        if (!is_dir($dataset_dir)) {
            mkdir($dataset_dir, 0777, true);
        }
        chmod($dataset_dir, 0777); 

        $file_name = $emp_id . '.jpg';
        $file_path = $dataset_dir . $file_name;

        if (file_put_contents($file_path, $file_data)) {
            $current_data = [];
            if (file_exists($db_file)) {
                $current_data = json_decode(file_get_contents($db_file), true) ?? [];
            }

            $exists = false;
            foreach ($current_data as &$emp) {
                if ($emp['id'] === $emp_id) {
                    $emp['name'] = $emp_name;
                    $emp['department'] = $emp_dept;
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $current_data[] = [
                    "id" => $emp_id,
                    "name" => $emp_name,
                    "department" => $emp_dept
                ];
            }

            $database_dir = __DIR__ . '/database/';
            if (!is_dir($database_dir)) {
                mkdir($database_dir, 0777, true);
            }
            file_put_contents($db_file, json_encode($current_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            $msg = "<p style='color: green; font-weight: bold;'>➔ Đăng ký thành công nhân viên: $emp_name</p>";
        } else {
            $msg = "<p style='color: red; font-weight: bold;'>Lỗi: Server không cho phép ghi file vào thư mục dataset!</p>";
        }
    } else {
        $msg = "<p style='color: red; font-weight: bold;'>Vui lòng điền đủ thông tin và chụp ảnh!</p>";
    }
}

// Đọc danh sách để hiển thị xuống bảng quản lý dưới giao diện
$employees_list = [];
if (file_exists($db_file)) {
    $employees_list = json_decode(file_get_contents($db_file), true) ?? [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng Quản Trị Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 20px; }
        .container { max-width: 950px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .flex { display: flex; gap: 20px; margin-top: 20px; }
        .form-panel { flex: 1; text-align: left; }
        .camera-panel { flex: 1; text-align: center; background: #eee; padding: 10px; border-radius: 8px; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 10px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        video, canvas { width: 100%; max-width: 320px; border-radius: 6px; background: #333; }
        .logout { float: right; background: #dc3545; text-decoration: none; color: white; padding: 8px 15px; border-radius: 4px; font-size: 14px; }
        
        /* CSS cho Bảng danh sách nhân viên */
        .list-panel { margin-top: 40px; border-top: 2px solid #eee; padding-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        th { background-color: #f1f1f1; font-weight: bold; color: #333; }
        tr:hover { background-color: #f9f9f9; }
        .btn-delete { color: #fff; background: #dc3545; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; transition: 0.2s; }
        .btn-delete:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_login.php?action=logout" class="logout">Đăng xuất</a>
        <a href="index.php" style="float: right; background: #007bff; text-decoration: none; color: white; padding: 8px 15px; border-radius: 4px; font-size: 14px; margin-right: 10px; font-weight: bold;">
            🖥️ Về trang Chấm Công
        </a>
        <h2>HỆ THỐNG ĐĂNG KÝ NHÂN VIÊN MỚI</h2>
        <?php if (!empty($msg)) echo $msg; ?>

        <form method="POST" id="regForm">
            <input type="hidden" name="action" value="register">
            <input type="hidden" name="image_base64" id="image_base64">

            <div class="flex">
                <div class="form-panel">
                    <label>Mã Nhân Viên:</label>
                    <input type="text" name="emp_id" placeholder="VD: NV001" required>

                    <label>Họ và Tên:</label>
                    <input type="text" name="emp_name" placeholder="VD: Nguyễn Văn A" required>

                    <label>Tên Ngành / Phòng ban:</label>
                    <input type="text" name="emp_dept" placeholder="VD: Công nghệ thông tin" required>
                    
                    <br><br>
                    <button type="button" id="submitBtn" style="width:100%; background:#007bff; font-size:16px;">LƯU NHÂN VIÊN</button>
                </div>

                <div class="camera-panel">
                    <h4>Webcam Chụp Ảnh Chân Dung</h4>
                    <video id="video" autoplay playsinline></video>
                    <canvas id="canvas" style="display:none;"></canvas>
                    <br>
                    <button type="button" id="snap" style="margin-top:10px;">Chụp Ảnh</button>
                    <div id="preview-text" style="margin-top:5px; color:#555; font-size:13px;">Chưa có ảnh chụp</div>
                </div>
            </div>
        </form>

        <div class="list-panel">
            <h3>DANH SÁCH NHÂN VIÊN ĐÃ ĐĂNG KÝ</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Mã Nhân Viên</th>
                        <th style="width: 30%;">Họ và Tên</th>
                        <th style="width: 25%;">Ngành / Phòng ban</th>
                        <th style="width: 15%;">Ảnh Chân Dung</th>
                        <th style="width: 15%;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($employees_list)): ?>
                        <?php foreach ($employees_list as $emp): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($emp['id']); ?></strong></td>
                                <td style="text-align: left;"><?php echo htmlspecialchars($emp['name']); ?></td>
                                <td style="text-align: left;"><?php echo htmlspecialchars($emp['department']); ?></td>
                                <td>
                                    <?php 
                                    // Tạo đường dẫn ảnh để hiển thị kiểm tra
                                    $img_src = "dataset/" . $emp['id'] . ".jpg";
                                    if(file_exists(__DIR__ . "/" . $img_src)){
                                        echo '<img src="'.$img_src.'?t='.time().'" width="55" style="border-radius:4px; border:1px solid #ccc; display:block; margin:0 auto;">';
                                    } else {
                                        echo '<span style="color:#aaa; font-size:12px;">Không có ảnh</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="admin_dashboard.php?action=delete&id=<?php echo urlencode($emp['id']); ?>" 
                                       class="btn-delete"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên [<?php echo htmlspecialchars($emp['name']); ?>] ra khỏi hệ thống?');">
                                        Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="color: #999; font-style: italic; padding: 20px;">Hệ thống chưa có nhân viên nào. Vui lòng đăng ký!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const snap = document.getElementById('snap');
        const imageBase64Input = document.getElementById('image_base64');
        const previewText = document.getElementById('preview-text');

        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then((stream) => { video.srcObject = stream; })
            .catch((err) => { alert("Không thể mở Webcam: " + err); });

        snap.addEventListener('click', () => {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);
            
            const dataURL = canvas.toDataURL('image/jpeg');
            imageBase64Input.value = dataURL;
            previewText.innerText = "✅ Đã chụp ảnh thành công!";
            previewText.style.color = "green";
        });

        document.getElementById('submitBtn').addEventListener('click', () => {
            if(!imageBase64Input.value){
                alert("Bạn phải bấm nút 'Chụp Ảnh' trước khi lưu!");
                return;
            }
            document.getElementById('regForm').submit();
        });
    </script>
</body>
</html>