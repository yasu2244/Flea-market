document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.tab-link');
    const container = document.getElementById('item-list-container');

    links.forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        links.forEach(l => l.classList.remove('active'));
        link.classList.add('active');

        const tab = link.dataset.tab;
        fetch(`/mypage/switch-tab?tab=${tab}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => { container.innerHTML = html; })
        .catch(err => { console.error(err); });
      });
    });
  });
