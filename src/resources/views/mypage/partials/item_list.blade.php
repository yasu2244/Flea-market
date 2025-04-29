@php
  use Illuminate\Support\Str;
@endphp

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
            @php
              $path = $item->image_path;
              $url  = Str::startsWith($path, 'assets/')
                      ? asset($path)
                      : asset('storage/' . $path);
            @endphp
            <img src="{{ $url }}" alt="{{ $item->name }}" class="item-image">
            @if(!empty($item->is_sold))
              <span class="sold-label">SOLD</span>
            @endif
          </div>
        </a>
        <p class="item-name">{{ $item->name }}</p>
      </div>
    @endforeach
  @endif
</div>
