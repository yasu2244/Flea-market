@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
@endsection

@section('content')
@if (session('status'))
    <div class="alert-message">
        {{ session('status') }}
    </div>
@endif

{{-- 429エラー（ログイン試行制限）のみを表示 --}}
@if ($errors->has('throttle'))
    <div class="error-container">
        <p class="error-message">{{ $errors->first('throttle') }}</p>
    </div>
@endif

@if (session('error'))
    <div class="error-container">
        <p class="error-message">{{ session('error') }}</p>
    </div>
@endif

<div class="login-container">
    <h2>ログイン</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス:</label>
            <input type="text" name="email" id="email" value="{{ old('email') }}">
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード:</label>
            <input type="password" name="password" id="password">
            @error('password')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="login-btn">ログインする</button>
    </form>

    <p class="register-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </p>
</div>
@endsection
