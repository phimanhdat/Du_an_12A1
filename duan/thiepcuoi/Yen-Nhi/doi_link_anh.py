import re
from urllib.parse import urlparse

INPUT_FILE = "index.php"
OUTPUT_FILE = "index.php"  # ghi đè

# 👉 Bạn chỉ cần dán link cũ vào đây
OLD_LINK = "https://w.ladicdn.com/s800x1050/675faed0e377b9028f9ce15b/z6160088731512_df66749b0fd9c74021b34e5bdcf5872e-20241224021329-1nhhi.jpg"

# 👉 Link mới
NEW_LINK = "https://raw.githubusercontent.com/phimanhdat/Manh-Dat/main/Yen_Nhi_18_12/z7342886027181_678f7411b24dda3aa124df6268a05e81.jpg"
# === TÁCH ĐUÔI FILE TỰ ĐỘNG ===
parsed = urlparse(OLD_LINK)
image_suffix = re.escape(parsed.path.lstrip("/"))

with open(INPUT_FILE, "r", encoding="utf-8") as f:
    content = f.read()

# === BẮT MỌI LINK KẾT THÚC BẰNG ĐUÔI FILE ===
pattern = rf"https?://[^\s\"']*{image_suffix}"

new_content, count = re.subn(pattern, NEW_LINK, content)

with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
    f.write(new_content)

print(f"✅ Đã thay {count} link ảnh có cùng đuôi")
