<div class="item-list">
    @if($tab === 'purchased' && $items->isEmpty())
      <p>購入済みの商品はまだありません。</p>
    @elseif($tab === 'listed' && $items->isEmpty())
      <p>出品した商品はまだありません。</p>
    @else
      @foreach($items as $item)
        <div class="item-card">
          <a href="{{ route('items.show', $item->id) }}">
            <div class="item-image-wrapper">
              <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" class="item-image">
              @if(!empty($item->is_sold) && $item->is_sold)
                <span class="sold-label">SOLD</span>
              @endif
            </div>
          </a>
          <p class="item-name">{{ $item->name }}</p>
        </div>
      @endforeach
    @endif
  </div>
