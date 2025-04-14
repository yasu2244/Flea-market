document.addEventListener('DOMContentLoaded', () => {
    const display = document.getElementById('customSelectDisplay');
    const options = document.getElementById('customSelectOptions');
    const hiddenInput = document.getElementById('payment_method_hidden');
    const summary = document.getElementById('summary-method');

    display.addEventListener('click', () => {
        display.style.display = 'none';
        options.style.display = 'block';
    });

    options.querySelectorAll('li').forEach(option => {
        option.addEventListener('click', () => {
            const value = option.dataset.value;

            hiddenInput.value = value;
            display.textContent = value;
            if (summary) summary.textContent = value;

            // 選択スタイル
            options.querySelectorAll('li').forEach(li => li.classList.remove('selected'));
            option.classList.add('selected');

            // 元に戻す
            options.style.display = 'none';
            display.style.display = 'block';
        });
    });
});
