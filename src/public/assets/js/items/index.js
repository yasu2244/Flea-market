document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.tab-link');

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();

            // タブ切り替え
            links.forEach(l => l.classList.remove('active'));
            link.classList.add('active');

            const tab = link.dataset.tab;

            fetch(`/items/switch-tab?tab=${tab}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('item-list-container').innerHTML = html;
            })
            .catch(err => {
                console.error('タブの切り替えに失敗しました:', err);
            });
        });
    });
});
