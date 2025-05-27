document.addEventListener('DOMContentLoaded', () => {
    const links     = document.querySelectorAll('.tab-link');
    const container = document.getElementById('item-list-container');

    links.forEach(link => {
      link.addEventListener('click', async e => {
        e.preventDefault();

        links.forEach(l => l.classList.remove('active'));
        link.classList.add('active');

        const tab = link.dataset.tab;  // 'sell', 'buy', or 'chat'
        try {
          const res = await fetch(`/mypage/switch-tab?tab=${tab}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          });
          if (!res.ok) throw new Error(res.statusText);
          const html = await res.text();
          container.innerHTML = html;

          // ここでアドレスバーを書き換える
          history.pushState(null, '', `/mypage?tab=${tab}`);
        } catch (err) {
          console.error('タブ切替エラー', err);
        }
      });
    });
  });
