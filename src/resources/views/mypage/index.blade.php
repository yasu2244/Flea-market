@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/mypage/index.css') }}">
@endsection

@section('content')
@if (session('status'))
    <div class="alert-message">
        {{ session('status') }}
    </div>
@endif

<div class="mypage-header">
    <div class="profile-image-container">
        @php
        // 画像パスを取得
        $imgPath = optional($user->profile)->profile_image;
        @endphp

        <div class="profile-image-wrapper">
            @if($imgPath)
                @if(\Illuminate\Support\Str::startsWith($imgPath, 'assets/'))
                    {{-- シーダーで入れた assets 内のテスト画像 --}}
                    <img
                    src="{{ asset($imgPath) }}"
                    alt="プロフィール画像"
                    class="profile-image"
                    >
                @else
                    {{-- storage/app/public 以下に保存された画像 --}}
                    <img
                    src="{{ asset('storage/' . $imgPath) }}"
                    alt="プロフィール画像"
                    class="profile-image"
                    >
                @endif
            @else
            <div class="profile-avatar-placeholder"></div>
            @endif
        </div>

        <div class="profile-info">
            <p class="profile-name">
                {{ optional($user->profile)->name ?: $user->name }}
            </p>

            @if($user->average_rating > 0)
                <div class="user-rating">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="star {{ $i <= $user->average_rating ? 'selected' : '' }}">★</span>
                @endfor
                </div>
            @endif
        </div>

        <a href="{{ route('mypage.profile.edit') }}" class="profile-edit-btn">
            プロフィール編集
        </a>
    </div>
</div>

<ul class="tab-menu">
    <li>
        <a href="{{ url('/mypage?tab=sell') }}"
            class="tab-link {{ $tab==='sell'?'active':'' }}"
            data-tab="sell">
            出品した商品
        </a>
    </li>
    <li>
        <a href="{{ url('/mypage?tab=buy') }}"
            class="tab-link {{ $tab==='buy'?'active':'' }}"
            data-tab="buy">
            購入した商品
        </a>
    </li>
    <li>
        <a href="{{ url('/mypage?tab=chat') }}"
                class="tab-link {{ $tab==='chat'?'active':'' }}"
                data-tab="chat">
                取引中の商品
            <span class="tab-badge"
                    @if($chatRoomCount === 0) style="display:none" @endif>
                {{ $chatRoomCount }}
            </span>
        </a>
    </li>
</ul>

<div id="item-list-container">
    @if($tab === 'chat')
        @include('mypage.partials.chat_room_list', ['rooms' => $rooms])
    @else
        @include('mypage.partials.item_list', ['items' => $items, 'tab' => $tab])
    @endif
</div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/mypage/index.js') }}"></script>
@endsection
