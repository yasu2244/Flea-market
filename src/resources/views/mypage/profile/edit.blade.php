@extends('layouts.app')

@section('css')
    {{-- 作成ページと同じCSSを流用 --}}
    <link rel="stylesheet" href="{{ asset('assets/css/profile/create.css') }}">
@endsection

@php
    use Illuminate\Support\Str;
    // 現在の画像パスを取得
    $img = Auth::user()->profile->profile_image;
@endphp

@section('content')
<div class="profile-container">
    <h2>プロフィール編集</h2>

    <form method="POST"
          action="{{ route('mypage.profile.update') }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- プロフィール画像と画像選択ボタン --}}
        <div class="profile-image-container">
            <div class="profile-image-wrapper">
                @if($img)
                    @if(Str::startsWith($img, 'assets/'))
                        {{-- シーダー画像 --}}
                        <img
                            id="preview"
                            class="profile-image selected"
                            src="{{ asset($img) }}"
                            alt="プロフィール画像"
                        >
                    @else
                        {{-- ストレージ画像 --}}
                        <img
                            id="preview"
                            class="profile-image selected"
                            src="{{ asset('storage/' . $img) }}"
                            alt="プロフィール画像"
                        >
                    @endif
                @else
                    <img
                        id="preview"
                        class="profile-image"
                        src=""
                        alt="プロフィール画像"
                    >
                @endif
            </div>

            <label for="profile_image" class="image-select-btn">画像を選択する</label>
            <input
                type="file"
                id="profile_image"
                name="profile_image"
                accept="image/*"
                style="display: none;"
            >
        </div>

        <div class="form-group">
            <label for="name">ユーザー名:</label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', Auth::user()->profile->name) }}"
            >
            @error('name')<p class="error-message">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="postal_code">郵便番号:</label>
            <input
                type="text"
                name="postal_code"
                id="postal_code"
                value="{{ old('postal_code', Auth::user()->profile->postal_code) }}"
            >
            @error('postal_code')<p class="error-message">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="address">住所:</label>
            <input
                type="text"
                name="address"
                id="address"
                value="{{ old('address', Auth::user()->profile->address) }}"
            >
            @error('address')<p class="error-message">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="building">建物名:</label>
            <input
                type="text"
                name="building"
                id="building"
                value="{{ old('building', Auth::user()->profile->building) }}"
            >
            @error('building')<p class="error-message">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="update-btn">更新する</button>
    </form>
</div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/profile.js') }}"></script>
@endsection
