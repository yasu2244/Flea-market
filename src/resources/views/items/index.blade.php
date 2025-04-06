@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/items/index.css') }}">
@endsection

@section('content')
<div class="item-list">
    @forelse($items as $item)
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
    @empty
        <p>商品が見つかりません。</p>
    @endforelse
</div>
@endsection
