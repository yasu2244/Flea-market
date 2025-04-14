function updatePaymentMethod(select) {
    const value = select.value;
    document.getElementById('summary-method').textContent = value || '選択なし';
    document.getElementById('payment_method_hidden').value = value;
}
