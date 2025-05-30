document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('chat-image-input');
    const container = document.getElementById('image-preview-container');

    if (!input || !container) return;

    // 画像選択時にプレビュー表示
    input.addEventListener('change', () => {
        // 既存プレビューをクリア
        container.innerHTML = '';

        Array.from(input.files).forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = e => {
                const card = document.createElement('div');
                card.className = 'image-preview';
                card.innerHTML = `
                    <img src="${e.target.result}" alt="preview${idx}">
                    <button type="button" class="remove-btn">&times;</button>
                `;
                // 削除ボタン
                card.querySelector('.remove-btn').addEventListener('click', () => {
                    // ファイル入力をクリア
                    input.value = '';
                    // プレビュー消去
                    container.innerHTML = '';
                });
                container.appendChild(card);
            };
            reader.readAsDataURL(file);
        });
    });
});
