document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('openCompleteModal');
    const overlay = document.getElementById('completeModal');
    const stars   = Array.from(document.querySelectorAll('.star-rating .star'));
    const submit  = document.getElementById('evaluateSubmit');
    let rating    = 0;

    overlay.addEventListener('click', e => {
        if (e.target === overlay) overlay.style.display = 'none';
    });

    // 星をクリックして rating を更新
    stars.forEach(star => {
        const val = parseInt(star.dataset.value, 10);

        // ホバー時
        star.addEventListener('mouseenter', () => {
            stars.forEach(s => {
                s.classList.toggle('hovered', parseInt(s.dataset.value, 10) <= val);
            });
        });
        // ホバー解除
        star.addEventListener('mouseleave', () => {
            stars.forEach(s => s.classList.remove('hovered'));
        });

        // クリックで選択
        star.addEventListener('click', () => {
        rating = val;
        stars.forEach(s => {
            s.classList.toggle('selected',
                parseInt(s.dataset.value, 10) <= rating
            );
        });
        submit.disabled = false;
        submit.classList.add('enabled');
        });
    });

      stars.forEach(star => {
        star.addEventListener('mouseenter', () => {
        const hoverVal = parseInt(star.dataset.value, 10);
            stars.forEach(s => {
                s.classList.toggle('hovered',
                    parseInt(s.dataset.value, 10) <= hoverVal
                );
            });
        });
        star.addEventListener('mouseleave', () => {
            // ホバーが外れたらすべての hovered をクリア
            stars.forEach(s => s.classList.remove('hovered'));
        });
    });

    // 送信ボタン押下で動的フォーム送信
    openBtn.addEventListener('click', () => {
        rating = 0;
        stars.forEach(s => s.classList.remove('selected'));
        submit.disabled = true;
        submit.classList.remove('enabled');
        overlay.style.display = 'flex';
    });
    overlay.addEventListener('click', e => {
        if (e.target === overlay) overlay.style.display = 'none';
    });
    submit.addEventListener('click', () => {
        if (!rating) return;
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const form  = document.createElement('form');
        form.method = 'POST';
        form.action = openBtn.dataset.evaluateUrl;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${token}">
            <input type="hidden" name="rating" value="${rating}">
        `;
        document.body.appendChild(form);
        form.submit();
    });
});
