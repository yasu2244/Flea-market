@php
  use Illuminate\Support\Str;
@endphp

<div class="item-list">
    {{-- マイリストタブかつ未認証は何も出力しない --}}
    @if($tab === 'mylist' && ! Auth::check())
        {{-- blank --}}

    {{-- アイテムが空ならメッセージ --}}
    @elseif($items->isEmpty())
        <p>商品が見つかりません。</p>

    {{-- 通常のアイテム一覧 --}}
    @else
        @foreach($items as $item)
            <div class="item-card">
                <a href="{{ route('items.show', $item) }}">
                    <div class="item-image-wrapper">
                        @php
                            $path = $item->image_path;
                            $url  = Str::startsWith($path, 'assets/')
                                    ? asset($path)
                                    : asset('storage/' . $path);
                        @endphp
                        <img src="{{ $url }}" alt="{{ $item->name }}" class="item-image">
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

