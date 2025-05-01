@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/verify-email.css') }}">
@endsection

@section('content')
    <div class="verify-container">
        @if (session('status'))
            <div class="flash-message">
                {{ session('status') }}
            </div>
        @endif

        <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
        <p>メール認証を完了してください。</p>

        @php
            $user = Auth::user();
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            );
        @endphp

        <a href="{{ $verificationUrl }}" class="verify-button">認証はこちらから</a>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit">認証メールを再送する</button>
        </form>
    </div>
@endsection
