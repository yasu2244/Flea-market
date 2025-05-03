document.addEventListener('DOMContentLoaded', () => {
    const display     = document.getElementById('customSelectDisplay');
    const options     = document.getElementById('customSelectOptions');
    const hiddenInput = document.getElementById('payment_method_hidden');
    const summary     = document.getElementById('summary-method');

    display.addEventListener('click', () => {
        // 高さはそのまま、透明に
        display.style.visibility = 'hidden';
        options.style.display = 'block';
    });

    options.querySelectorAll('li').forEach(option => {
        option.addEventListener('click', () => {
            // 既存選択をクリアして selected クラスを追加
            options.querySelectorAll('li').forEach(li => {
                li.classList.remove('selected');
            });
            // クリックした項目に 'selected' を付与
            option.classList.add('selected');

            // 選んだテキストを表示する
            display.textContent = option.textContent;
            // hidden には id (数値) を保存
            hiddenInput.value = option.dataset.value;
            // 購入ページのサマリーにも反映（存在チェック）
            if (summary) summary.textContent = option.textContent;

            // メニューを閉じて display を戻す
            options.style.display    = 'none';
            display.style.visibility = 'visible';
        });
    });
});


