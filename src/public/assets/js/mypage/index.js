document.addEventListener('DOMContentLoaded', () => {
    const links     = document.querySelectorAll('.tab-link');
    const container = document.getElementById('item-list-container');
    const chatBadge = document.querySelector('.tab-link[data-tab="chat"] .tab-badge');

    links.forEach(link => {
        link.addEventListener('click', async e => {
        e.preventDefault();
        // アクティブ状態の更新
        links.forEach(l => l.classList.remove('active'));
        link.classList.add('active');

        const tab = link.dataset.tab;
        try {
            const res = await fetch(`/mypage/switch-tab?tab=${tab}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error(res.statusText);

            const json = await res.json();
            // コンテンツ差し替え
            container.innerHTML = json.html;

            // 取引中タブのバッジ更新
            if (tab === 'chat') {
            const count = json.roomCount;
            if (count > 0) {
                chatBadge.textContent   = count;
                chatBadge.style.display = '';
            } else {
                chatBadge.style.display = 'none';
            }
            }

            // アドレスバーも書き換え
            history.pushState(null, '', `/mypage?tab=${tab}`);
        } catch (err) {
            console.error('タブ切替エラー', err);
        }
        });
    });
});
