document.addEventListener('DOMContentLoaded', () => {
    const links     = document.querySelectorAll('.tab-link');
    const container = document.getElementById('item-list-container');
    const urlParams = new URLSearchParams(window.location.search);
    const keyword   = urlParams.get('keyword');

    links.forEach(link => {
      link.addEventListener('click', async e => {
        e.preventDefault();

        links.forEach(l => l.classList.remove('active'));
        link.classList.add('active');

        const tab   = link.dataset.tab;  // 'recommend' or 'mylist'
        const query = keyword
          ? `?tab=${tab}&keyword=${encodeURIComponent(keyword)}`
          : `?tab=${tab}`;

        // 部分テンプレート取得
        const res  = await fetch(`/items/switch-tab${query}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const html = await res.text();
        container.innerHTML = html;

        history.pushState(null, '', `/${query}`);
      });
    });
  });
