// DOMContentLoaded イベントで画像プレビューの処理を設定
document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.getElementById("profile_image");
    const previewImage = document.getElementById("preview");

    if (fileInput && previewImage) {
        fileInput.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function () {
                previewImage.src = reader.result;
                previewImage.style.display = "block"; // プレビュー画像を表示
            };
            reader.readAsDataURL(file);
        });
    }
});
