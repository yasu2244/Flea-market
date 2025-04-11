document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.tab-link');
    const activeTab = document.querySelector('.tab-link.active');
    const urlParams = new URLSearchParams(window.location.search);
    const keyword = urlParams.get('keyword');

    // 検索キーワードがあるときは初期タブ読み込みをスキップ
    if (activeTab && !keyword) {
        loadTab(activeTab.dataset.tab, keyword);
    }

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            links.forEach(l => l.classList.remove('active'));
            link.classList.add('active');

            const tab = link.dataset.tab;
            loadTab(tab, keyword);
        });
    });

    function loadTab(tab, keyword) {
        const query = keyword ? `?tab=${tab}&keyword=${encodeURIComponent(keyword)}` : `?tab=${tab}`;

        fetch(`/items/switch-tab${query}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('item-list-container').innerHTML = html;
        })
        .catch(err => {
            console.error('タブの読み込みに失敗しました:', err);
        });
    }
});
