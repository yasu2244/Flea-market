@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/register.css') }}">
@endsection

@section('content')
<div class="register-container">
    <h2>会員登録</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name') <p class="error-message">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
            @error('email') <p class="error-message">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" required>
            @error('password') <p class="error-message">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">確認用パスワード</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
            @error('password_confirmation') <p class="error-message">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="register-btn">登録する</button>
    </form>

    <p class="login-link">
        <a href="{{ route('login') }}">ログインはこちら</a>
    </p>
</div>
@endsection
