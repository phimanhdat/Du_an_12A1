INPUT_FILE = "index.php"
OUTPUT_FILE = "index.php"
while True:
    A_OLD = input("nhập link cần thay : ")
    A_NEW = input("nhập link thay : ")
    with open(INPUT_FILE, "r", encoding="utf-8") as f:
        content = f.read()
    content = content.replace(A_OLD, A_NEW)
    with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
        f.write(content)
    print("✅ Đã thay xong link A & B → index_out.php")
