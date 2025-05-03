@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/purchase/show.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    {{-- 左側 --}}
    <div class="item-section">
        @php
            use Illuminate\Support\Str;
            $path = $item->image_path;
            $url  = Str::startsWith($path, 'assets/')
                    ? asset($path)
                    : asset('storage/' . $path);
        @endphp

        {{-- 上段：画像＋商品情報 --}}
        <div class="item-top">
            <img src="{{ $url }}" alt="{{ $item->name }}" class="item-image">
            <div class="item-details">
                <h2>{{ $item->name }}</h2>
                <p class="price">
                    <span class="yen">¥</span>{{ number_format($item->price) }}
                </p>
            </div>
        </div>

        {{-- 中段：支払い方法 --}}
        <div class="payment-section">
            <p class="payment-label" id="payment-label">支払い方法</p>

            <div class="custom-select-wrapper">
                <div class="custom-select-display" id="customSelectDisplay">
                    支払い方法を選択
                </div>
                <ul class="custom-select-options" id="customSelectOptions">
                    <li data-value="コンビニ支払い">コンビニ払い</li>
                    <li data-value="カード支払い">カード支払い</li>
                </ul>
            </div>

            @error('payment_method')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 下段：配送先 --}}
        <div class="shipping-section">
            <div class="section-header">
                <p>配送先</p>
                <a href="{{ route('purchase.address.edit', $item->id) }}" class="change-link">変更する</a>
            </div>
            <div class="shipping-address">
                @php
                    $address = session('purchase_address');
                @endphp
                <p>〒 {{ $address['postal_code'] ?? $user->profile->postal_code }}</p>
                <p>{{ $address['address'] ?? $user->profile->address }}</p>
                <p>{{ $address['building'] ?? $user->profile->building }}</p>
            </div>

            @error('shipping_address')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- 右側：支払いサマリー＋購入ボタン --}}
    <div class="summary-box">
        <form method="POST" action="{{ route('purchase.store', $item->id) }}">
            @csrf

            <div class="summary-table">
                <div class="summary-row">
                    <span>商品代金</span>
                    <span>¥{{ number_format($item->price) }}</span>
                </div>
                <div class="summary-row">
                    <span>支払い方法</span>
                    <span id="summary-method">選択なし</span>
                </div>
            </div>

            {{-- 左カラムの選択結果をここで送信 --}}
            <input type="hidden" id="payment_method_hidden" name="payment_method"
            value="{{ old('payment_method') }}">

            <input type="hidden" name="shipping_address"
                value="{{ old('shipping_address', $user->profile->postal_code . ' ' . $user->profile->address . ' ' . $user->profile->building) }}">

            <button type="submit" class="purchase-button">購入する</button>
        </form>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/customSelect.js') }}"></script>
@endsection
