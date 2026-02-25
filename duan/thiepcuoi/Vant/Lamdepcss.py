import re

def safe_beautify(css: str) -> str:
    # Xuống dòng sau { } ;
    css = css.replace('{', '{\n')
    css = css.replace('}', '}\n')
    css = css.replace(';', ';\n')

    # Nếu có "#" mà trước đó không phải newline => xuống dòng
    css = re.sub(r'([^\n])(#)', r'\1\n#', css)

    # Loại bỏ nhiều dòng trống (chỉ giữ tối đa 1)
    css = re.sub(r'\n+', '\n', css)

    return css.strip() + "\n"


# chỉ xử lý phần trong <style id="style_element">
def beautify_in_html(html: str) -> str:
    pattern = r'(<style id="style_ladi"[^>]*>)(.*?)(</style>)'
    m = re.search(pattern, html, flags=re.DOTALL)

    if not m:
        print("Không tìm thấy <style id='style_element'>")
        return html

    before = m.group(1)
    css_raw = m.group(2)
    after = m.group(3)

    css_new = safe_beautify(css_raw)

    return before + "\n" + css_new + after


# ---- Chạy file ----
if __name__ == "__main__":
    with open("index.html", "r", encoding="utf-8") as f:
        html = f.read()

    result = beautify_in_html(html)

    with open("output.html", "w", encoding="utf-8") as f:
        f.write(result)

    print("Đã xong! CSS đã được format an toàn vào output.html")
