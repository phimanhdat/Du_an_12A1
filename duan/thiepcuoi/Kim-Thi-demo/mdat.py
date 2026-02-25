import re
import json

# ===== CẤU HÌNH =====
INPUT_FILE = "index.php"        # file HTML
OLD_VALUE = "IMAGE80"
NEW_VALUE = "IMAGE8011"
SCRIPT_ID = "script_event_data"
# ====================


def replace_value(obj, old, new):
    """Đệ quy thay giá trị trong JSON"""
    if isinstance(obj, dict):
        return {
            (new if k == old else k): replace_value(v, old, new)
            for k, v in obj.items()
        }
    elif isinstance(obj, list):
        return [replace_value(i, old, new) for i in obj]
    elif isinstance(obj, str):
        return new if obj == old else obj
    else:
        return obj


with open(INPUT_FILE, "r", encoding="utf-8") as f:
    html = f.read()

# Tìm thẻ script đúng ID
pattern = rf'<script[^>]*id="{SCRIPT_ID}"[^>]*>(.*?)</script>'
match = re.search(pattern, html, re.DOTALL)

if not match:
    print("❌ Không tìm thấy script_event_data")
    exit()

json_text = match.group(1).strip()

# Parse JSON
data = json.loads(json_text)

# Thay giá trị
new_data = replace_value(data, OLD_VALUE, NEW_VALUE)

# JSON mới
new_json = json.dumps(new_data, ensure_ascii=False, indent=2)

# Ghi lại HTML
new_html = html.replace(json_text, new_json)

with open(INPUT_FILE, "w", encoding="utf-8") as f:
    f.write(new_html)

print("✅ Đã thay thành công")
