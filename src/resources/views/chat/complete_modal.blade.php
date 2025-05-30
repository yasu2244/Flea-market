<div id="completeModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <h3 class="modal-title">取引が完了しました。</h3>
        <hr class="modal-divider">

        <p class="modal-subtitle">今回の取引相手はどうでしたか？</p>

        <div class="star-rating">
        @for ($i = 1; $i <= 5; $i++)
            <span class="star" data-value="{{ $i }}">★</span>
        @endfor
        </div>
        <hr class="modal-divider">

        <button id="evaluateSubmit" class="btn-submit" disabled>送信する</button>
    </div>
</div>
