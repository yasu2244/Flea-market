
document.addEventListener('DOMContentLoaded', () => {
    const imageInput = document.getElementById('image');
    const preview    = document.getElementById('preview');
    const button     = document.querySelector('.file-upload-button');

    // ボタンをクリックするとファイル選択
    button.addEventListener('click', () => imageInput.click());

    imageInput.addEventListener('change', e => {
      const file = e.target.files[0];
      if (!file) return;

      // プレビュー表示
      preview.src = URL.createObjectURL(file);
      preview.style.display = 'block';

      // 画像が表示されたらボタンを非表示に
      button.style.display = 'none';
    });
  });
