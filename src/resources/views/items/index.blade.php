@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/items/index.css') }}">
@endsection

@section('content')
    <ul class="tab-menu">
        <li><a href="#" class="tab-link active" data-tab="recommend">おすすめ</a></li>
        <li><a href="#" class="tab-link" data-tab="mylist">マイリスト</a></li>
    </ul>

    <div id="item-list-container">
        @include('items.partials.item_list', ['items' => $items, 'tab' => $tab ?? 'recommend'])
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/items/index.js') }}"></script>
@endsection
