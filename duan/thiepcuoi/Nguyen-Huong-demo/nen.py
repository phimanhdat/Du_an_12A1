import os
from PIL import Image

# 📂 Thư mục gốc chứa ảnh
input_folder = "anh"
# 📂 Thư mục lưu ảnh sau khi nén
output_folder = "th"

# ⚙️ Giới hạn kích thước (200KB)
MAX_SIZE = 200 * 1024  # 200KB

# 🔧 Tạo thư mục đầu ra nếu chưa có
os.makedirs(output_folder, exist_ok=True)

def compress_image(file_path, output_path):
    img = Image.open(file_path)
    img = img.convert("RGB")

    # 🔻 Resize nhẹ ban đầu (giúp nén dễ đạt dưới 200KB)
    max_dimension = 1600  # hoặc 1280 nếu bạn muốn nén mạnh hơn
    img.thumbnail((max_dimension, max_dimension))

    quality = 85
    step = 5

    while True:
        img.save(output_path, "JPEG", optimize=True, quality=quality)
        size = os.path.getsize(output_path)

        if size <= MAX_SIZE or quality <= 20:
            break

        # Giảm chất lượng dần
        quality -= step

        # Nếu đã giảm hết mà vẫn lớn, resize nhỏ hơn chút
        if quality <= 20 and size > MAX_SIZE:
            width, height = img.size
            img = img.resize((int(width * 0.9), int(height * 0.9)))
            quality = 85  # reset lại chất lượng

    print(f"✅ {os.path.basename(file_path)} → {round(size/1024, 1)} KB (quality={quality})")

# 🔄 Nén toàn bộ ảnh trong thư mục pst
for filename in os.listdir(input_folder):
    if filename.lower().endswith(('.jpg', '.jpeg', '.png', '.webp', '.bmp', '.tiff')):
        input_path = os.path.join(input_folder, filename)
        output_path = os.path.join(output_folder, os.path.splitext(filename)[0] + ".jpg")
        compress_image(input_path, output_path)

print("\n🎯 Hoàn tất nén ảnh xuống dưới 200KB!")
