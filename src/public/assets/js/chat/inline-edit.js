document.addEventListener('DOMContentLoaded', () => {
    // 編集ボタン押下で編集 UI を開く
    document.querySelectorAll('.js-edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
        const row = btn.closest('.message-row');
        const id  = row.dataset.msgId;
        row.querySelector(`.js-body-${id}`).style.display    = 'none';
        row.querySelector(`.js-actions-${id}`).style.display = 'none';
        row.querySelector(`.js-form-${id}`).style.display    = 'block';
        });
    });

    // キャンセル
    document.querySelectorAll('.js-cancel').forEach(btn => {
        btn.addEventListener('click', () => {
        const row = btn.closest('.message-row');
        const id  = row.dataset.msgId;
        row.querySelector(`.js-form-${id}`).style.display    = 'none';
        row.querySelector(`.js-body-${id}`).style.display    = '';
        row.querySelector(`.js-actions-${id}`).style.display = '';
        });
    });

    // 保存ボタンで PUT 送信
    document.querySelectorAll('.js-save-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const row = btn.closest('.message-row');
            const id  = row.dataset.msgId;
            const container = row.querySelector(`.js-form-${id}`);
            const url = container.dataset.updateUrl;
            const textarea = container.querySelector('textarea[name="body"]');
            const body    = textarea.value.trim();

            // 簡易バリデーション
            if (!body) {
                alert('本文を入力してください');
                return;
            }

            // CSRF トークンを取得
            const token = document.querySelector('meta[name="csrf-token"]').content;

            try {
                const res = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN' : token,
                    'Accept'       : 'application/json',
                },
                body: JSON.stringify({ body })
            });

            if (!res.ok) {
                if (res.status === 422) {
                    const json = await res.json();
                    alert(json.errors.body.join('\n'));
                    return;
                }
                throw new Error(`Unexpected status: ${res.status}`);
            }

            const json = await res.json();
            // 成功時：本文を置き換えて、UI を戻す
            const bodyDiv = row.querySelector(`.js-body-${id}`);
            bodyDiv.innerHTML = json.body;
            container.style.display    = 'none';
            row.querySelector(`.js-actions-${id}`).style.display = '';
            bodyDiv.style.display = '';
        } catch (err) {
            console.error(err);
            alert('更新に失敗しました。再度お試しください。');
        }
        });
    });
});
