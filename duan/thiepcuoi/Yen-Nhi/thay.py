import re
import os

def replace_names(content):
    # Thay đầy đủ trước
    content = re.sub(r'\bXuân Thịnh\b', 'Duy Tâm', content, flags=re.IGNORECASE)
    content = re.sub(r'\bDiễm Hằng\b', 'yến Nhi', content, flags=re.IGNORECASE)
    content = re.sub(r'\bThịnh\b', 'Tâm', content, flags=re.IGNORECASE)
    content = re.sub(r'\bHằng\b', 'Nhi', content, flags=re.IGNORECASE)


    return content


def process_file(file_path):
    with open(file_path, "r", encoding="utf-8") as f:
        content = f.read()

    new_content = replace_names(content)

    with open(file_path, "w", encoding="utf-8") as f:
        f.write(new_content)

    print("Đã xử lý:", file_path)


# Xử lý 1 file
process_file("index.php")
