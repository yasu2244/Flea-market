@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品一覧</h1>
    <div class="row">
        @foreach($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="{{ $product->image_path ? asset('storage/' . $product->image_path) : asset('images/no-image.png') }}" class="card-img-top" alt="商品画像">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">ブランド: {{ $product->brand ?? 'なし' }}</p>
                        <p class="card-text">価格: ¥{{ number_format($product->price) }}</p>
                        <p class="card-text">状態: {{ $product->status->name }}</p>
                        @if($product->is_sold)
                            <span class="badge bg-danger">SOLD</span>
                        @endif
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary">詳細を見る</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
