# ====== CONFIG ======
BASE_URL = "https://raw.githubusercontent.com/phimanhdat/Manh-Dat/main/"
INPUT_FILE = "ds.txt"
OUTPUT_FILE = "out.txt"
# ====================

with open(INPUT_FILE, "r", encoding="utf-8") as f:
    lines = f.readlines()

result = []

for line in lines:
    path = line.strip()
    if not path:
        continue  # bỏ dòng trống

    # nếu đã là link http thì bỏ qua
    if path.startswith("http"):
        result.append(path)
    else:
        full_link = BASE_URL + path
        result.append(full_link)

with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
    f.write("\n".join(result))

print(f"✅ Done! Đã xuất {len(result)} link vào {OUTPUT_FILE}")
