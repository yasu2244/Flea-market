@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/items/show.css') }}">
@endsection

@section('content')
<div class="container">
    {{-- 左：商品画像 --}}
    <div class="left-column">
        <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}">
    </div>

    {{-- 右：商品情報 --}}
    <div class="right-column">
        <h2 class="item-name">{{ $item->name }}</h2>
        <p class="item-brand">{{ $item->brand ?? 'ブランド名なし' }}</p>
        <p class="item-price">
            <span class="yen">¥</span>{{ number_format($item->price) }}
            <span class="item-tax">(税込)</span>
        </p>

        {{-- いいね・コメント表示 --}}
        <div class="favorite-comment">
            @php
                $liked = Auth::check() ? $item->isLikedBy(Auth::user()) : false;
            @endphp
            <div class="like-button" data-item-id="{{ $item->id }}"
            data-auth="{{ Auth::check() ? 'true' : 'false' }}">
                <i class="{{ $liked ? 'fas fa-star liked' : 'far fa-star' }}"></i>
                <div class="like-count">{{ $item->likes->count() }}</div>
            </div>

            {{-- コメント数 --}}
            <div class="comment-icon">
                <i class="far fa-comment"></i>
                <div class="comment-count">{{ $item->comments->count() }}</div>
            </div>
        </div>

        {{-- 購入ボタン --}}
        @auth
            <a href="{{ route('purchase.show', $item->id) }}" class="purchase-button">購入手続きへ</a>
        @else
            <a href="{{ route('login') }}" class="purchase-button"
            onclick="alert('購入手続きを利用するにはログインが必要です');">購入手続きへ</a>
        @endauth


        {{-- 商品説明 --}}
        <h3 class="section-title">商品説明</h3>
        <p>{!! nl2br(e($item->description)) !!}</p>

        {{-- 商品情報 --}}
        <h3 class="section-title">商品の情報</h3>
        <p><strong>カテゴリー：</strong>
            @foreach($item->categories as $category)
                <span class="category-tag">{{ $category->name }}</span>
            @endforeach
        </p>
        <p><strong>商品の状態：</strong>{{ $item->status->name }}</p>

        {{-- コメント一覧 --}}
        <h3 class="section-title">コメント ({{ $item->comments->count() }})</h3>
        @forelse($item->comments as $comment)
            <div class="comment-box">
                {{-- プロフィール画像 --}}
                @if ($comment->user && $comment->user->profile && $comment->user->profile->profile_image)
                    <img src="{{ asset($comment->user->profile->profile_image) }}" alt="user">
                @else
                    <div class="comment-avatar-placeholder"></div>
                @endif


                <div>
                    {{-- プロフィール名 --}}
                    <div class="comment-user">
                        {{ $comment->user && $comment->user->profile ? $comment->user->profile->name : '（削除されたユーザー）' }}
                    </div>

                    {{-- コメント本文 --}}
                    <p class="comment-content">{{ $comment->content }}</p>
                </div>
            </div>
        @empty
            <p>コメントはまだありません。</p>
        @endforelse

        {{-- コメント投稿フォーム --}}
        <h3 class="section-title">商品へのコメント</h3>
        <form action="{{ route('comments.store', $item->id) }}" method="POST" class="comment-form">
            @csrf
            <textarea name="content" rows="5">{{ old('content') }}</textarea>
            @error('content')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            <button type="submit">コメントを投稿する</button>
        </form>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/like.js') }}"></script>
@endsection
