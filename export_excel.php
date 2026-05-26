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
//...

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