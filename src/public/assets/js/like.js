document.addEventListener('DOMContentLoaded', function () {
    const likeBtn = document.querySelector('.like-button');

    if (!likeBtn) return;

    const isAuth = likeBtn.dataset.auth === 'true';

    likeBtn.addEventListener('click', async function () {
        if (!isAuth) {
            alert('いいねするにはログインが必要です');
            return;
        }

        const itemId = this.dataset.itemId;
        const icon = this.querySelector('i');
        const count = this.querySelector('.like-count');

        try {
            const res = await fetch(`/items/${itemId}/toggle-like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            });

            const data = await res.json();

            if (data.liked) {
                icon.classList.remove('far');
                icon.classList.add('fas', 'liked');
            } else {
                icon.classList.remove('fas', 'liked');
                icon.classList.add('far');
            }

            count.textContent = data.like_count;
        } catch (err) {
            console.error('通信エラー', err);
        }
    });
});
