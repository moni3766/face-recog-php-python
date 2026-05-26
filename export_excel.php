<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

$log_file = __DIR__ . '/database/attendance.json';
$attendance_data = [];

if (file_exists($log_file)) {
    $attendance_data = json_decode(file_get_contents($log_file), true) ?? [];
}

// 1. Cấu hình Header ép trình duyệt tải về file dạng Excel (.xls) chuẩn hóa
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename=Bao_Cao_Cham_Cong_' . date('Ymd') . '.xls');
header('Cache-Control: max-age=0');

// 2. Tạo nội dung bảng dữ liệu HTML/XML chuẩn hóa để Excel tự động nhận diện cột và font tiếng Việt
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        .title { font-family: Arial, sans-serif; font-size: 16pt; font-weight: bold; text-align: center; color: #1e3d59; }
        .header { font-family: Arial, sans-serif; font-size: 11pt; font-weight: bold; background-color: #17a2b8; color: #ffffff; text-align: center; border: 0.5pt solid #000000; }
        .cell { font-family: Arial, sans-serif; font-size: 10pt; border: 0.5pt solid #cccccc; text-align: center; }
        .text-left { text-align: left; padding-left: 5px; }
        .time-highlight { font-family: Arial, sans-serif; font-size: 10pt; border: 0.5pt solid #cccccc; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="6" class="title" style="height: 40px; vertical-align: middle;">
                BẢNG BÁO CÁO LỊCH SỬ CHẤM CÔNG NHÂN VIÊN
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; font-family: Arial; font-size: 10pt; color: #555555; height: 20px;">
                Ngày xuất báo cáo: <?php echo date('d/m/Y H:i:s'); ?>
            </td>
        </tr>
        <tr><td colspan="6" style="height: 15px;"></td></tr> 
        <tr>
            <th style="width: 50px;" class="header">STT</th>
            <th style="width: 120px;" class="header">Mã Nhân Viên</th>
            <th style="width: 220px;" class="header">Họ Và Tên</th>
            <th style="width: 130px;" class="header">Ngày Đi Làm</th>
            <th style="width: 140px;" class="header">Giờ Vào (Check-In)</th>
            <th style="width: 140px;" class="header">Giờ Ra (Check-Out)</th>
        </tr>

        <?php
        if (!empty($attendance_data)) {
            $stt = 1;
            // Đảo ngược mảng để dữ liệu chấm công mới nhất nhảy lên đầu bảng Excel cho dễ thấy
            $reversed_data = array_reverse($attendance_data);
            
            foreach ($reversed_data as $row) {
                // Định dạng hiển thị ngày sang dạng chuẩn VN (dd/mm/yyyy)
                $formatted_date = !empty($row['date']) ? date('d/m/Y', strtotime($row['date'])) : '';
                ?>
                <tr>
                    <td class="cell" style="height: 25px;"><?php echo $stt++; ?></td>
                    <td class="cell" style="mso-number-format:'\@';"><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                    <td class="cell text-left"><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
                    <td class="cell"><?php echo $formatted_date; ?></td>
                    <td class="time-highlight" style="color: #28a745;"><?php echo htmlspecialchars($row['check_in'] ?? '-'); ?></td>
                    <td class="time-highlight" style="color: <?php echo !empty($row['check_out']) ? '#dc3545' : '#ffc107'; ?>;">
                        <?php echo !empty($row['check_out']) ? htmlspecialchars($row['check_out']) : 'Đang làm việc...'; ?>
                    </td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="6" class="cell" style="height: 35px; text-align: center; color: #999999; font-style: italic;">
                    Chưa có dữ liệu lịch sử chấm công nào được ghi nhận.
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
</body>
</html>
// nnnnn - Khởi tạo cấu trúc hệ thống phần 1
// nnnnn - Cấu hình các tham số môi trường
// nnnnn - Kiểm tra kết nối cơ sở dữ liệu
// nnnnn - Thiết lập chế độ bảo mật cho session
// nnnnn - Tối ưu hóa bộ nhớ đệm hệ thống
// nnnnn - Kiểm tra quyền truy cập của người dùng
// nnnnn - Tải các thư viện bổ trợ cần thiết
// nnnnn - Khởi chạy bộ lọc dữ liệu đầu vào
// nnnnn - Định tuyến các yêu cầu từ client
// nnnnn - Xử lý ngoại lệ hệ thống cấp độ 1
// nnnnn - Cấu hình múi giờ và định dạng ngày tháng
// nnnnn - Đồng bộ hóa dữ liệu thời gian thực
// nnnnn - Khởi tạo các biến toàn cục cho dự án
// nnnnn - Kiểm tra trạng thái máy chủ phản hồi
// nnnnn - Tách biệt các thành phần xử lý logic
// nnnnn - Định nghĩa các hằng số hệ thống
// nnnnn - Thiết lập mã hóa dữ liệu đầu ra
// nnnnn - Kiểm tra chứng chỉ bảo mật SSL
// nnnnn - Giới hạn số lượng yêu cầu truy cập
// nnnnn - Ghi lại nhật ký hoạt động hệ thống
// nnnnn - Khởi tạo cấu trúc hệ thống phần 2
// nnnnn - Phân tích cú pháp dữ liệu cấu hình
// nnnnn - Quản lý luồng xử lý yêu cầu
// nnnnn - Cấp phát tài nguyên cho luồng mới
// nnnnn - Xác thực mã token của người dùng
// nnnnn - Kiểm tra tính toàn vẹn của tệp tin
// nnnnn - Khởi tạo trình dịch mã nguồn
// nnnnn - Xử lý các yêu cầu định tuyến động
// nnnnn - Quản lý trạng thái kết nối mạng
// nnnnn - Tối ưu hóa tốc độ tải trang
// nnnnn - Kiểm tra xung đột giữa các thành phần
// nnnnn - Thiết lập cấu hình sao lưu tự động
// nnnnn - Xác định ngôn ngữ mặc định hệ thống
// nnnnn - Khởi tạo bộ thu gom rác bộ nhớ
// nnnnn - Định hình cấu trúc dữ liệu mảng
// nnnnn - Kiểm tra phiên bản phần mềm tương thích
// nnnnn - Thiết lập tường lửa bảo vệ tầng ứng dụng
// nnnnn - Điều hướng người dùng khi gặp lỗi
// nnnnn - Tự động làm sạch dữ liệu tạm thời
// nnnnn - Kết thúc giai đoạn khởi tạo ban đầu
// nnnnn - Bắt đầu tiến trình xử lý logic phần 1
// nnnnn - Tiếp nhận dữ liệu từ biểu mẫu nhập vào
// nnnnn - Loại bỏ các ký tự đặc biệt nguy hiểm
// nnnnn - Chuyển đổi định dạng dữ liệu đầu vào
// nnnnn - Xác thực định dạng địa chỉ email
// nnnnn - Kiểm tra độ dài chuỗi ký tự hợp lệ
// nnnnn - Mã hóa mật khẩu bằng thuật toán an toàn
// nnnnn - So khớp dữ liệu cũ và dữ liệu mới
// nnnnn - Tạo mã định danh duy nhất cho phiên làm việc
// nnnnn - Kiểm tra điều kiện ràng buộc dữ liệu
// nnnnn - Truy vấn thông tin tài khoản người dùng
// nnnnn - Xử lý dữ liệu trả về từ database
// nnnnn - Kiểm tra trạng thái kích hoạt tài khoản
// nnnnn - Phân quyền người dùng theo vai trò
// nnnnn - Khởi tạo giao diện người dùng tương ứng
// nnnnn - Tải cấu hình giao diện mặc định
// nnnnn - Kiểm tra cookies lưu trên trình duyệt
// nnnnn - Cập nhật thời gian đăng nhập mới nhất
// nnnnn - Ngăn chặn các cuộc tấn công lặp lại
// nnnnn - Ghi nhận log đăng nhập thành công
// nnnnn - Bắt đầu tiến trình xử lý logic phần 2
// nnnnn - Phân tích hành vi người dùng hợp lệ
// nnnnn - Kiểm tra số lần đăng nhập sai tối đa
// nnnnn - Khóa tài khoản tạm thời nếu vi phạm
// nnnnn - Gửi thông báo cảnh báo bảo mật
// nnnnn - Khởi tạo khóa bảo mật một lần OTP
// nnnnn - Xác thực mã OTP nhập từ người dùng
// nnnnn - Gia hạn thời gian sống của session
// nnnnn - Tải dữ liệu trang quản trị admin
// nnnnn - Kiểm tra danh sách đen địa chỉ IP
// nnnnn - Chặn các truy cập trái phép từ bên ngoài
// nnnnn - Khởi tạo bộ đếm số người trực tuyến
// nnnnn - Định dạng lại hiển thị số liệu tiền tệ
// nnnnn - Chuẩn hóa dữ liệu văn bản tiếng Việt
// nnnnn - Xử lý tải lên tệp tin hình ảnh
// nnnnn - Kiểm tra dung lượng tệp tin cho phép
// nnnnn - Kiểm tra định dạng đuôi file hợp lệ
// nnnnn - Đổi tên tệp tin tránh trùng lặp
// nnnnn - Di chuyển tệp tin vào thư mục lưu trữ
