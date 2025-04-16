@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/purchase/address_edit.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <h2>配送先変更</h2>

    <form method="POST" action="{{ route('purchase.address.update', $item->id) }}">
        @csrf

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}">
            @error('postal_code')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address') }}">
            @error('address')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building') }}">
            @error('building')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="update-btn">更新する</button>
    </form>
</div>
@endsection
