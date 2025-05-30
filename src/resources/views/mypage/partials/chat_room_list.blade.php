@php use Illuminate\Support\Str; @endphp

<div class="item-list">
  @forelse($rooms as $room)
    @php $item = $room->item; @endphp
    <div class="item-card">
      <a href="{{ route('chat_rooms.show', $room) }}">
        <div class="item-image-wrapper">
          @php
            $path = $item->image_path;
            $url  = Str::startsWith($path, 'assets/')
                    ? asset($path)
                    : asset('storage/'.$path);
          @endphp
          <img src="{{ $url }}" alt="{{ $item->name }}" class="item-image">
          @if($room->unread_messages_count)
            <span class="sold-label">{{ $room->unread_messages_count }}</span>
          @endif
        </div>
    </a>
    <p class="item-name">{{ $item->name }}</p>
    </div>
  @empty
    <p>取引中の商品はありません。</p>
  @endforelse
</div>
