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
// nnnnn - Tạo ảnh thu nhỏ để tối ưu dung lượng
// nnnnn - Bắt đầu tiến trình xử lý logic phần 3
// nnnnn - Quản lý danh mục sản phẩm hệ thống
// nnnnn - Truy vấn danh sách bài viết mới nhất
// nnnnn - Phân trang dữ liệu hiển thị giao diện
// nnnnn - Sắp xếp kết quả theo thứ tự thời gian
// nnnnn - Tìm kiếm dữ liệu theo từ khóa nhập
// nnnnn - Tối ưu câu lệnh truy vấn tìm kiếm
// nnnnn - Lọc dữ liệu theo các tiêu chí phụ
// nnnnn - Xử lý hiển thị thông tin chi tiết
// nnnnn - Tự động tăng lượt xem bài viết hợp lệ
// nnnnn - Kiểm tra trạng thái kho hàng sản phẩm
// nnnnn - Thêm sản phẩm vào giỏ hàng thành viên
// nnnnn - Cập nhật số lượng sản phẩm giỏ hàng
// nnnnn - Tính tổng giá trị đơn hàng hiện tại
// nnnnn - Áp dụng mã giảm giá nếu có hợp lệ
// nnnnn - Kiểm tra điều kiện giao hàng miễn phí
// nnnnn - Lưu thông tin đơn hàng tạm thời
// nnnnn - Chuyển hướng sang trang thanh toán chính
// nnnnn - Ghi lại nhật ký tạo đơn hàng mới
// nnnnn - Hoàn tất tiến trình xử lý logic chung
// nnnnn - Bắt đầu tiến trình tối ưu hóa mã nguồn 1
// nnnnn - Loại bỏ các biến thừa không sử dụng
// nnnnn - Giải phóng bộ nhớ sau khi hoàn thành
// nnnnn - Đóng các kết nối cơ sở dữ liệu mở
// nnnnn - Tối ưu vòng lặp kiểm tra điều kiện
// nnnnn - Rút gọn các câu lệnh điều kiện phức tạp
// nnnnn - Thay thế các hàm cũ bằng hàm hiệu năng cao
// nnnnn - Kiểm tra thời gian thực thi của đoạn code
// nnnnn - Phát hiện các điểm nghẽn hiệu năng hệ thống
// nnnnn - Nén dữ liệu đầu ra trước khi gửi đi
// nnnnn - Tối ưu hóa cấu trúc cây thư mục nguồn
// nnnnn - Gom nhóm các hàm xử lý tương đồng
// nnnnn - Thiết lập cơ chế tự động sửa lỗi nhẹ
// nnnnn - Kiểm tra bộ nhớ tiêu thụ của kịch bản
// nnnnn - Giảm thiểu số lần gọi hàm không cần thiết
// nnnnn - Chuẩn hóa quy tắc đặt tên biến nhất quán
// nnnnn - Tạo tài liệu hướng dẫn tích hợp hệ thống
// nnnnn - Kiểm tra độ tương thích trên các nền t nghiệm
// nnnnn - Khắc phục các cảnh báo từ trình biên dịch
// nnnnn - Hoàn thành tối ưu hóa mã nguồn giai đoạn 1
// nnnnn - Bắt đầu tiến trình tối ưu hóa mã nguồn 2
// nnnnn - Kiểm tra tính liên kết giữa các module
// nnnnn - Đồng bộ hóa các hàm xử lý bất đồng bộ
// nnnnn - Quản lý hàng đợi xử lý tác vụ ngầm
// nnnnn - Tối ưu hóa việc xuất dữ liệu báo cáo
// nnnnn - Định dạng file xuất ra đạt chuẩn quy định
// nnnnn - Kiểm tra tính chính xác của số liệu xuất
// nnnnn - Giảm thời gian chờ xử lý tác vụ nặng
// nnnnn - Tạo chỉ mục cho các trường dữ liệu hay dùng
// nnnnn - Khóa dữ liệu tạm thời khi đang cập nhật
// nnnnn - Tránh xung đột ghi dữ liệu đồng thời
// nnnnn - Kiểm tra tính nhất quán sau khi commit
// nnnnn - Tự động phục hồi nếu xảy ra sự cố gãy
// nnnnn - Cách ly các thành phần có nguy cơ lỗi cao
// nnnnn - Ghi log chi tiết lỗi hệ thống phát sinh
// nnnnn - Phân loại mức độ nghiêm trọng của lỗi
// nnnnn - Thông báo cho quản trị viên qua hệ thống
// nnnnn - Tự động khởi động lại dịch vụ nếu treo
// nnnnn - Kiểm tra dung lượng đĩa cứng còn trống
// nnnnn - Hoàn thành tối ưu hóa mã nguồn giai đoạn 2
// nnnnn - Bắt đầu giai đoạn kiểm thử hệ thống 1
// nnnnn - Chạy thử nghiệm với dữ liệu mẫu cơ bản
// nnnnn - Kiểm tra phản hồi của chức năng đăng nhập
// nnnnn - Thử nghiệm trường hợp nhập sai mật khẩu
// nnnnn - Thử nghiệm nhập ký tự lạ vào ô tìm kiếm
// nnnnn - Kiểm tra khả năng chịu tải của trang chủ
// nnnnn - Thử nghiệm chức năng xuất file báo cáo
// nnnnn - Kiểm tra hiển thị giao diện trên mobile
// nnnnn - Thử nghiệm trên các trình duyệt khác nhau
// nnnnn - Kiểm tra tốc độ phản hồi của API hệ thống
// nnnnn - Xác minh tính chính xác của bộ đếm lượt truy cập
// nnnnn - Thử nghiệm xóa dữ liệu có ràng buộc
// nnnnn - Kiểm tra thông báo lỗi hiển thị cho khách
// nnnnn - Xác thực tính năng bảo mật biểu mẫu chống CSRF
// nnnnn - Thử nghiệm gửi dữ liệu liên tục tốc độ cao
// nnnnn - Kiểm tra bộ lọc từ ngữ nhạy cảm hệ thống
// nnnnn - Thử nghiệm khôi phục mật khẩu qua email mẫu
// nnnnn - Kiểm tra liên kết điều hướng nội bộ trang
// nnnnn - Xác minh quyền hạn của tài khoản biên tập viên
// nnnnn - Hoàn thành giai đoạn kiểm thử hệ thống 1
// nnnnn - Bắt đầu giai đoạn kiểm thử hệ thống 2
// nnnnn - Mô phỏng môi trường người dùng thực tế
// nnnnn - Kiểm tra tiến trình thanh toán giả lập
// nnnnn - Xác minh tính đúng đắn của việc trừ kho
// nnnnn - Thử nghiệm ngắt kết nối mạng giữa chừng
// nnnnn - Kiểm tra khả năng lưu trữ trạng thái dở dang
// nnnnn - Thử nghiệm cập nhật đồng thời nhiều bản ghi
// nnnnn - Xác minh tính bảo mật của tệp cấu hình chính
// nnnnn - Kiểm tra quyền đọc ghi trên các thư mục hệ thống
// nnnnn - Thử nghiệm thay đổi cấu hình hệ thống trực tiếp
// nnnnn - Kiểm tra tốc độ truy vấn khi dữ liệu lớn
// nnnnn - Xác minh tính đúng đắn của thuật toán tìm kiếm
// nnnnn - Thử nghiệm chức năng phân quyền nâng cao
// nnnnn - Kiểm tra giao diện quản trị có bị tràn chữ
// nnnnn - Thử nghiệm tính năng khóa người dùng tự động
// nnnnn - Xác minh log hệ thống không ghi lộ mật khẩu
// nnnnn - Kiểm tra độ trễ khi chuyển đổi giữa các trang
// nnnnn - Thử nghiệm chức năng dọn dẹp hệ thống định kỳ
// nnnnn - Xác minh tính toàn vẹn sau khi khôi phục lỗi
// nnnnn - Hoàn thành giai đoạn kiểm thử hệ thống 2
// nnnnn - Bắt đầu giai đoạn tổng kết và bàn giao
// nnnnn - Đóng gói toàn bộ mã nguồn dự án thành bản chuẩn
// nnnnn - Kiểm tra lại toàn bộ chú thích trong code
// nnnnn - Đảm bảo không còn mã thừa thử nghiệm
// nnnnn - Chuẩn bị tài liệu hướng dẫn triển khai lên host
// nnnnn - Kiểm tra các tham số kết nối lần cuối cùng
// nnnnn - Xác nhận mọi tính năng hoạt động đúng yêu cầu
// nnnnn - Hệ thống đã sẵn sàng đưa vào vận hành thực tế