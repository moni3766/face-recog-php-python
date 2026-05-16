import sys
import os
import json
import face_recognition

# ÉP HỆ THỐNG WINDOWS XUẤT DỮ LIỆU CHỮ (STDOUT) THEO CHUẨN UTF-8 ĐỂ ĐỌC TIẾNG VIỆT CÓ DẤU
sys.stdout.reconfigure(encoding='utf-8')

def load_employee_db(db_path):
    if os.path.exists(db_path):
        with open(db_path, 'r', encoding='utf-8') as f:
            try:
                return json.load(f)
            except:
                return []
    return []

def main():
    if len(sys.argv) < 2:
        print(json.dumps({"status": "error", "message": "No image path provided"}))
        return

    target_image_path = sys.argv[1]
    
    # Đường dẫn động dựa vào vị trí file script
    current_dir = os.path.dirname(__file__)
    dataset_dir = os.path.abspath(os.path.join(current_dir, '../dataset'))
    db_path = os.path.abspath(os.path.join(current_dir, '../database/employees.json'))

    employee_db = load_employee_db(db_path)
    
    known_encodings = []
    known_ids = [] # Lưu mã nhân viên tương ứng với ảnh

    # 1. Quét thư mục ảnh dataset
    if os.path.exists(dataset_dir):
        for file_name in os.listdir(dataset_dir):
            if file_name.endswith(('.jpg', '.png', '.jpeg')):
                path = os.path.join(dataset_dir, file_name)
                try:
                    img = face_recognition.load_image_file(path)
                    encoding = face_recognition.face_encodings(img)[0]
                    known_encodings.append(encoding)
                    
                    emp_id = os.path.splitext(file_name)[0]
                    known_ids.append(emp_id)
                except:
                    continue

    if not known_encodings:
        print(json.dumps({"status": "error", "message": "He thong chua co du lieu nhan vien"}))
        return

    try:
        # 2. Nhận diện ảnh đầu vào
        target_img = face_recognition.load_image_file(target_image_path)
        target_encodings = face_recognition.face_encodings(target_img)

        if len(target_encodings) == 0:
            print(json.dumps({"status": "unknown", "message": "Khong tim thay khuon mat"}))
            return

        target_encoding = target_encodings[0]
        matches = face_recognition.compare_faces(known_encodings, target_encoding, tolerance=0.5)

        if True in matches:
            match_index = matches.index(True)
            matched_id = known_ids[match_index]
            
            # Tìm thông tin chi tiết từ file JSON
            employee_info = next((emp for emp in employee_db if emp['id'] == matched_id), None)
            
            if employee_info:
                print(json.dumps({
                    "status": "success",
                    "id": employee_info['id'],
                    "name": employee_info['name'],
                    "department": employee_info['department']
                }, ensure_ascii=False))
            else:
                print(json.dumps({"status": "success", "id": matched_id, "name": "Chưa cập nhật thông tin", "department": "-"}))
        else:
            print(json.dumps({"status": "failed"}))

    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}))

if __name__ == "__main__":
    main()