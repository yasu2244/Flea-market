document.addEventListener('DOMContentLoaded', () => {
  const form     = document.querySelector('.js-send-form');
  if (!form) return;
  const textarea = form.querySelector('textarea[name="body"]');
  if (!textarea) return;

  const roomId = form.dataset.roomId;
  const key    = `chat_draft_${roomId}`;

  // 読み込み時に下書きを復元
  const draft = localStorage.getItem(key);
  if (draft) {
    textarea.value = draft;
  }

  // 入力時に下書きを保存
  textarea.addEventListener('input', () => {
    localStorage.setItem(key, textarea.value);
  });

  // 送信時に下書きをクリア
  form.addEventListener('submit', () => {
    localStorage.removeItem(key);
  });
});
