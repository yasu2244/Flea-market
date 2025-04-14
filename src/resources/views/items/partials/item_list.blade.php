<div class="item-list">
    @if($tab === 'mylist' && !Auth::check())
        <p>ログインしていいねしたものが表示されます。</p>
    @elseif($items->isEmpty())
        <p>商品が見つかりません。</p>
    @else
        @foreach($items as $item)
            <div class="item-card">
                <a href="{{ route('items.show', $item->id) }}">
                    <div class="item-image-wrapper">
                        <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" class="item-image">
                        @if($item->is_sold)
                            <span class="sold-label">SOLD</span>
                        @endif
                    </div>
                </a>
                <p class="item-name">{{ $item->name }}</p>
            </div>
        @endforeach
    @endif
</div>
