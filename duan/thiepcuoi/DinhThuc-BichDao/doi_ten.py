import os

folder = "th"  # đường dẫn tới thư mục ảnh

# Lấy danh sách file ảnh .jpg
files = [f for f in os.listdir(folder) if f.lower().endswith(".jpg")]
files.sort()  # sắp xếp theo tên

for i, filename in enumerate(files, start=1):
    old_path = os.path.join(folder, filename)
    new_name = f"{i}.jpg"
    new_path = os.path.join(folder, new_name)

    os.rename(old_path, new_path)

print("✅ Đổi tên hoàn tất!")
