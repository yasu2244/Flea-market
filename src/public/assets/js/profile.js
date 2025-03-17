document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.getElementById("profile_image");
    const previewImage = document.getElementById("preview");

    fileInput.addEventListener("change", function (event) {
        var reader = new FileReader();
        reader.onload = function () {
            previewImage.src = reader.result;
            previewImage.style.display = "block"; // ✅ 画像を表示
        };
        reader.readAsDataURL(event.target.files[0]);
    });
});
