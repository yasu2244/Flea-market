@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/items/show.css') }}">

<div class="container">
    {{-- 左：商品画像 --}}
    <div class="left-column">
        <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}">
    </div>

    {{-- 右：商品情報 --}}
    <div class="right-column">
        <h2 class="item-name">{{ $item->name }}</h2>
        <p class="item-brand">{{ $item->brand ?? 'ブランド名なし' }}</p>
        <p class="item-price">¥{{ number_format($item->price) }} <span style="font-size: 14px; color: #888;">(税込)</span></p>

        <div class="favorite-comment">
            <i class="far fa-heart"></i> 3
            <i class="far fa-comment ms-3"></i> {{ $item->comments->count() }}
        </div>

        {{-- 購入ボタン --}}
        <a href="#" class="purchase-button">購入手続きへ</a>

        {{-- 商品説明 --}}
        <h5 class="section-title">商品説明</h5>
        <p>{!! nl2br(e($item->description)) !!}</p>

        {{-- 商品情報 --}}
        <h5 class="section-title">商品の情報</h5>
        <p>カテゴリー：
            @foreach($item->categories as $category)
                <span class="category-tag">{{ $category->name }}</span>
            @endforeach
        </p>
        <p>商品の状態：{{ $item->status->name }}</p>

        {{-- コメント一覧 --}}
        <h5 class="section-title">コメント ({{ $item->comments->count() }})</h5>
        @forelse($item->comments as $comment)
            <div class="comment-box">
                <i class="far fa-user-circle"></i>
                <div>
                    <strong>{{ $comment->user->name ?? 'ゲスト' }}</strong>
                    <p class="comment-content">{{ $comment->content }}</p>
                </div>
            </div>
        @empty
            <p>コメントはまだありません。</p>
        @endforelse

        {{-- コメント投稿フォーム --}}
        @auth
            <form action="{{ route('comments.store', $item->id) }}" method="POST" class="comment-form">
                @csrf
                <textarea name="content" rows="3">{{ old('content') }}</textarea>
                @error('content')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
                <button type="submit">コメントを投稿する</button>
            </form>
        @endauth

    </div>
</div>
@endsection
