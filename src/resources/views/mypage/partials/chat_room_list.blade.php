@php use Illuminate\Support\Str; @endphp

<ul class="chat-room-list">
  @forelse($rooms as $room)
    @php $item = $room->item; @endphp
    <li class="chat-room-item">
      <a href="{{ route('chat_rooms.show', $room) }}">
        <div class="item-thumb">
          @php
            $path = $item->image_path;
            $url  = Str::startsWith($path, 'assets/')
                    ? asset($path)
                    : asset('storage/'.$path);
          @endphp
          <img src="{{ $url }}" alt="{{ $item->name }}">
          @if($room->unread_messages_count)
            <span class="badge">{{ $room->unread_messages_count }}</span>
          @endif
        </div>
        <div class="item-info">
          <p class="item-name">{{ $item->name }}</p>
          <p class="last-updated">{{ $room->updated_at->format('Y/m/d H:i') }}</p>
        </div>
      </a>
    </li>
  @empty
    <p>取引中の商品はありません。</p>
  @endforelse
</ul>
