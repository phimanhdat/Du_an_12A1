document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("rsvpForm");

    form.addEventListener("submit", function(e) {
        e.preventDefault(); // không reload trang

        const formData = new FormData(form);

        fetch("save.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            alert(result.message);   // thông báo từ save.php

            if (result.success) {
                form.reset();        // xóa form sau khi gửi thành công
            }
        })
        .catch(error => {
            console.error("Lỗi gửi dữ liệu:", error);
            alert("Không thể gửi phản hồi!");
        });
    });

});
