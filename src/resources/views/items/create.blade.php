@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/items/create.css') }}">
@endsection

@section('content')
<div class="form-container">
    <h2 class="section-title">商品の出品</h2>

    <form method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- 1. 商品画像 --}}
    <div class="field-group">
      <label for="image" class="field-label">商品画像</label>
      <div class="file-upload-container">
        <img id="preview" class="preview-image" alt="プレビュー">
        <button type="button" class="file-upload-button">画像を選択する</button>
        <input type="file" name="image" id="image" accept="image/*">
      </div>
      @error('image')<p class="error-message">{{ $message }}</p>@enderror
    </div>

    {{-- 2. 商品の詳細 --}}
    <h3 class="section-subtitle">商品の詳細</h3>

    {{-- カテゴリ --}}
    <div class="field-group">
        <label class="field-label">カテゴリ</label>
        <div class="category-list">
            @foreach($categories as $c)
            <label class="category-item">
                <input
                type="checkbox"
                name="categories[]"
                value="{{ $c->id }}"
                {{ in_array($c->id, old('categories', [])) ? 'checked' : '' }}
                >
                <span class="category-text">{{ $c->name }}</span>
            </label>
            @endforeach
        </div>
        @error('categories')<p class="error-message">{{ $message }}</p>@enderror
    </div>

    {{-- 商品の状態 --}}
    <div class="field-group">
      <label class="status-label">商品の状態</label>
      <div class="custom-select-wrapper">
        <div class="custom-select-display" id="customSelectDisplay">
          {{ old('status_id')
             ? $statuses->firstWhere('id', old('status_id'))->name
             : '商品の状態を選択' }}
        </div>
        <ul class="custom-select-options" id="customSelectOptions">
          @foreach($statuses as $s)
            <li data-value="{{ $s->id }}"
                class="{{ old('status_id') == $s->id ? 'selected' : '' }}">
              {{ $s->name }}
            </li>
          @endforeach
        </ul>
        <input type="hidden" name="status_id" id="payment_method_hidden" value="{{ old('status_id') }}">
      </div>
      @error('status_id')<p class="error-message">{{ $message }}</p>@enderror
    </div>

    {{-- 3. 商品名と説明 --}}
    <h3 class="section-subtitle">商品名と説明</h3>

    <div class="field-group">
      <label for="name" class="field-label">商品名</label>
      <input type="text" name="name" id="name" value="{{ old('name') }}" class="input-text">
      @error('name')<p class="error-message">{{ $message }}</p>@enderror
    </div>

    <div class="field-group">
      <label for="brand" class="field-label">ブランド名</label>
      <input type="text" name="brand" id="brand" value="{{ old('brand') }}" class="input-text">
      @error('brand')<p class="error-message">{{ $message }}</p>@enderror
    </div>

    <div class="field-group">
      <label for="description" class="field-label">商品の説明</label>
      <textarea name="description" id="description" rows="4" class="textarea">{{ old('description') }}</textarea>
      @error('description')<p class="error-message">{{ $message }}</p>@enderror
    </div>

    <div class="field-group">
      <label for="price" class="field-label">販売価格</label>
      <div class="currency-field">
        <span class="currency-symbol">¥</span>
        <input type="text" name="price" id="price" value="{{ old('price') }}">
      </div>
      @error('price')<p class="error-message">{{ $message }}</p>@enderror
    </div>

    {{-- 4. 送信 --}}
    <div class="field-group">
      <button type="submit" class="submit-button">出品する</button>
    </div>
  </form>
</div>
@endsection

@section('js')
  <script src="{{ asset('assets/js/items/imageUploader.js') }}"></script>
  <script src="{{ asset('assets/js/customSelect.js') }}"></script>
@endsection
