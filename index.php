<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Ho_Chi_Minh'); // Đặt múi giờ Việt Nam

// XỬ LÝ KIỂM TRA NHẬN DIỆN KHUÔN MẶT BẰNG AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'scan_face') {
    header('Content-Type: application/json; charset=utf-8');
    
    $image_data = $_POST['image_base64'];
    if (empty($image_data)) {
        echo json_encode(["status" => "error", "message" => "Không nhận được dữ liệu ảnh từ Webcam"]);
        exit;
    }

    // 1. Lưu ảnh tạm từ camera quét vào thư mục uploads
    $upload_dir = __DIR__ . '/uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $image_data = str_replace('data:image/jpeg;base64,', '', $image_data);
    $image_data = str_replace(' ', '+', $image_data);
    $file_data = base64_decode($image_data);
    
    $target_file = $upload_dir . uniqid('snap_', true) . '.jpg';
    file_put_contents($target_file, $file_data);

    // 2. Đường dẫn tuyệt đối đến file chạy Python hệ thống của bạn
    $python_exe = 'C:\\laragon\\www\\face-recog\\core\\env\\Scripts\\python.exe';
    $py_script  = 'C:\\laragon\\www\\face-recog\\core\\ai_engine.py';
    
    // Mở luồng nhận dữ liệu UTF-8 từ Windows để đẩy sang Python không lỗi charmap
    $output_array = [];
    putenv('HEX_CHARSET=UTF-8');
    $cmd = 'chcp 65001 > nul && "' . $python_exe . '" "' . $py_script . '" "' . $target_file . '"';
    exec($cmd, $output_array);
    
    $output = '';
    foreach($output_array as $line) {
        if(strpos($line, 'status') !== false) {
            $output = $line;
            break;
        }
    }
    
    $result = json_decode($output, true);

    // Xóa ảnh tạm ngay sau khi AI quét xong để nhẹ máy
    if (file_exists($target_file)) unlink($target_file);

    // 3. Nếu tìm thấy khuôn mặt khớp trong dataset -> Tiến hành chấm công nhiều lần
    if ($result && isset($result['status']) && $result['status'] == 'success') {
        $emp_id = $result['id'];
        $emp_name = $result['name'];
        
        $today = date('Y-m-d');
        $now_time = date('H:i:s');
        
        $log_file = __DIR__ . '/database/attendance.json';
        $logs = [];
        if (file_exists($log_file)) {
            $logs = json_decode(file_get_contents($log_file), true) ?? [];
        }

        $type = "CHECK-IN (VÀO)";
        $last_index = -1;

        // Tìm lượt chấm công cuối cùng TRONG NGÀY HÔM NAY của nhân viên này
        foreach ($logs as $index => $log) {
            if ($log['id'] === $emp_id && $log['date'] === $today) {
                $last_index = $index; // Luôn bám theo lượt cuối cùng được ghi nhận
            }
        }

        // BIỆN PHÁP XỬ LÝ LOGIC CHẤM CÔNG NHIỀU LẦN TRONG NGÀY
        if ($last_index !== -1 && empty($logs[$last_index]['check_out'])) {
            // Nếu tìm thấy lượt hôm nay VÀ lượt đó chưa Check-out -> Điền giờ Ra
            $logs[$last_index]['check_out'] = $now_time;
            $type = "CHECK-OUT (RA)";
        } else {
            // Nếu chưa có lượt nào, hoặc lượt gần nhất ĐÃ CHECK-OUT XONG -> Tạo dòng mới (Vào ca mới)
            $logs[] = [
                "id" => $emp_id,
                "name" => $emp_name,
                "date" => $today,
                "check_in" => $now_time,
                "check_out" => ""
            ];
            $type = "CHECK-IN (VÀO)";
        }

        // Ghi dữ liệu chấm công lại vào file JSON
        file_put_contents($log_file, json_encode($logs, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $result['type'] = $type;
        $result['time'] = $now_time;
        echo json_encode($result);
        exit;
    }

    // Nếu không nhận diện được ai hoặc lỗi, trả về thông báo lỗi gốc
    if (!empty($output)) {
        echo $output;
    } else {
        echo json_encode(["status" => "failed", "message" => "Không thể nhận diện khuôn mặt này. Vui lòng thử lại!"]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cổng Nhận Diện Chấm Công</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #1e1e24; color: white; margin: 0; padding-top: 30px; }
        .wrapper { max-width: 500px; margin: 0 auto; background: #2a2a35; padding: 25px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        #video { width: 100%; max-width: 400px; border-radius: 8px; transform: scaleX(-1); border: 3px solid #007bff; background: #000; }
        .result-box { margin-top: 20px; padding: 15px; border-radius: 8px; background: #3a3a4c; display: none; text-align: left; border-left: 5px solid #28a745; }
        .success-text { color: #28a745; font-weight: bold; font-size: 18px; text-align: center; margin-top: 0; }
        .error-text { color: #dc3545; font-weight: bold; font-size: 16px; text-align: center; margin-top: 0; }
        .btn-scan { display: block; width: 100%; max-width: 400px; margin: 15px auto 0 auto; padding: 12px; background: #28a745; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn-scan:hover { background: #218838; }
        .btn-scan:disabled { background: #555; cursor: not-allowed; }
        .admin-link { display: inline-block; margin-top: 25px; color: #a0a0b0; text-decoration: none; font-size: 14px; }
        .admin-link:hover { color: #007bff; }
    </style>
</head>
<body>

    <div class="wrapper">
        <h2>CỔNG CHẤM CÔNG NHẬN DIỆN KHUÔN MẶT</h2>
        <p id="status-text" style="color: #ffc107;">Sẵn sàng. Nhìn vào camera và bấm nút bên dưới!</p>
        
        <video id="video" autoplay playsinline></video>
        <canvas id="canvas" style="display:none;"></canvas>

        <button class="btn-scan" id="scanBtn">BẤM ĐỂ CHẤM CÔNG</button>

        <div class="result-box" id="result-box">
            <p class="success-text" id="res-type">CHECK-IN THÀNH CÔNG</p>
            <hr style="border-color:#555">
            <p><b>Mã nhân viên:</b> <span id="res-id"></span></p>
            <p><b>Họ và tên:</b> <span id="res-name"></span></p>
            <p><b>Ngành/Phòng ban:</b> <span id="res-dept"></span></p>
            <p><b>Thời gian ghi nhận:</b> <span id="res-time" style="color:#ffc107; font-weight:bold;"></span></p>
        </div>
        <div style="margin-top: 20px;">
            <a href="export_excel.php" style="display: inline-block; padding: 8px 16px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 14px;">
                📥 XUẤT FILE EXCEL CHẤM CÔNG
            </a>
        </div>
        <br>
        <a href="admin_login.php" class="admin-link">➔ Vào trang quản trị thiết lập nhân viên</a>
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const scanBtn = document.getElementById('scanBtn');
        const resultBox = document.getElementById('result-box');
        const statusText = document.getElementById('status-text');

        // 1. Mở Webcam trực tiếp
        navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 }, audio: false })
            .then((stream) => { video.srcObject = stream; })
            .catch((err) => { 
                statusText.innerText = "❌ Lỗi: Không thể truy cập Camera!";
                alert("Vui lòng cho phép quyền truy cập Camera!"); 
            });

        // 2. Bắt sự kiện click nút để chụp và đối chiếu qua AI
        scanBtn.addEventListener('click', () => {
            scanBtn.disabled = true;
            scanBtn.innerText = "⏳ ĐANG ĐỐI CHIẾU AI...";
            statusText.innerText = "🔄 Đang xử lý ảnh dữ liệu...";
            resultBox.style.display = 'none';

            const context = canvas.getContext('2d');
            canvas.width = 640; 
            canvas.height = 480;
            
            // Vẽ ảnh webcam vào canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const base64Image = canvas.toDataURL('image/jpeg', 0.9);

            // Gửi dữ liệu bằng AJAX
            const formData = new FormData();
            formData.append('action', 'scan_face');
            formData.append('image_base64', base64Image);

            fetch('index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                scanBtn.disabled = false;
                scanBtn.innerText = "BẤM ĐỂ CHẤM CÔNG";

                if (data && data.status === 'success') {
                    // Đổi màu khung tùy theo trạng thái CHECK-IN hay CHECK-OUT
                    if(data.type.includes("VÀO")){
                        resultBox.style.borderLeftColor = "#28a745";
                        document.getElementById('res-type').className = "success-text";
                    } else {
                        resultBox.style.borderLeftColor = "#dc3545";
                        document.getElementById('res-type').className = "error-text";
                    }

                    // Đổ dữ liệu AI tìm được lên giao diện
                    document.getElementById('res-type').innerText = data.type + " THÀNH CÔNG";
                    document.getElementById('res-id').innerText = data.id;
                    document.getElementById('res-name').innerText = data.name;
                    document.getElementById('res-dept').innerText = data.department;
                    document.getElementById('res-time').innerText = data.time;
                    
                    resultBox.style.display = 'block';
                    statusText.innerText = "✅ Ghi nhận thành công: " + data.name;
                    statusText.style.color = "#28a745";

                } else {
                    statusText.innerText = "❌ " + (data.message || "Khuôn mặt không khớp với dữ liệu nhân viên!");
                    statusText.style.color = "#dc3545";
                }
            })
            .catch(err => {
                scanBtn.disabled = false;
                scanBtn.innerText = "BẤM ĐỂ CHẤM CÔNG";
                statusText.innerText = "❌ Lỗi kết nối đến cổng AI!";
                statusText.style.color = "#dc3545";
                console.error(err);
            });
        });
    </script>
</body>
</html>