document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('openCompleteModal');
    const overlay = document.getElementById('completeModal');
    const stars   = Array.from(document.querySelectorAll('.star-rating .star'));
    const submit  = document.getElementById('evaluateSubmit');
    let rating    = 0;

    // overlay・stars・submit がなければ何もしない（＝モーダル要素がないページではスルー）
    if (!overlay || stars.length === 0 || !submit) {
        return;
    }

    // オーバーレイ外クリックでモーダルを閉じる
    overlay.addEventListener('click', e => {
        if (e.target === overlay) overlay.style.display = 'none';
    });

    // 星アイコンのホバー／クリック処理
    stars.forEach(star => {
        const val = parseInt(star.dataset.value, 10);

        // ホバー: hovered クラスを付ける
        star.addEventListener('mouseenter', () => {
            stars.forEach(s => {
                const v = parseInt(s.dataset.value, 10);
                if (v <= val) {
                    s.classList.add('hovered');
                } else {
                    s.classList.remove('hovered');
                }
            });
        });
        star.addEventListener('mouseleave', () => {
            stars.forEach(s => s.classList.remove('hovered'));
        });

        // クリック: selected クラスを付けて評価を確定
        star.addEventListener('click', () => {
            rating = val;
            stars.forEach(s => {
                const v = parseInt(s.dataset.value, 10);
                if (v <= rating) {
                    s.classList.add('selected');
                } else {
                    s.classList.remove('selected');
                }
            });
            submit.disabled = false;
            submit.classList.add('enabled');
        });
    });

    // 「取引を完了する」ボタンが存在すれば、クリック時にモーダルを開く
    if (openBtn) {
        openBtn.addEventListener('click', () => {
            rating = 0;
            stars.forEach(s => {
                s.classList.remove('selected');
                s.classList.remove('hovered');
            });
            submit.disabled = true;
            submit.classList.remove('enabled');
            overlay.style.display = 'flex';
        });
    }

    if (!openBtn) {
        overlay.style.display = 'flex';
    }

    // 送信ボタン押下 → 動的フォーム作成＆送信
    submit.addEventListener('click', () => {
        if (!rating) {
            return;
        }
        const token = document.querySelector('meta[name="csrf-token"]').content;
        // モーダル自身の data-evaluate-url 属性を読む
        const actionUrl = overlay.dataset.evaluateUrl;
        const form  = document.createElement('form');
        form.method = 'POST';
        form.action = openBtn ? openBtn.dataset.evaluateUrl : overlay.dataset.evaluateUrl;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${token}">
            <input type="hidden" name="rating" value="${rating}">
        `;
        document.body.appendChild(form);
        form.submit();
    });
});
