@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
@endsection

@section('content')
<div class="login-container">
    <h2>ログイン</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス:</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
            @error('email') <p class="error-message">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード:</label>
            <input type="password" name="password" id="password" required>
            @error('password') <p class="error-message">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="login-btn">ログインする</button>
    </form>

    <p class="register-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </p>
</div>
@endsection
