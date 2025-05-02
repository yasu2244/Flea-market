@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/profile/create.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <h2>プロフィール設定</h2>

    <form method="POST" action="{{ route('mypage.profile.store') }}" enctype="multipart/form-data">
        @csrf
        {{-- プロフィール画像と画像選択ボタン --}}
        <div class="profile-image-container">
            <div class="profile-image-wrapper">
                <img id="preview" class="profile-image" src="" alt="プロフィール画像">
            </div>

            <label for="profile_image" class="image-select-btn">画像を選択する</label>
            <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;" onchange="previewImage(event)">
        </div>

        {{-- プロフィール情報フォーム --}}

        <div class="form-group">
            <label for="name">ユーザー名:</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="postal_code">郵便番号:</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}">
            @error('postal_code')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所:</label>
            <input type="text" name="address" id="address" value="{{ old('address') }}">
            @error('address')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名:</label>
            <input type="text" name="building" id="building" value="{{ old('building') }}">
            @error('building')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>


        <button type="submit" class="update-btn">更新する</button>
    </form>
</div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/profile.js') }}"></script>
@endsection
