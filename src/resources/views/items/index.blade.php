@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/items/index.css') }}">
@endsection

@section('content')
@if (session('status'))
    <div class="alert-message">
        {{ session('status') }}
    </div>
@endif

<ul class="tab-menu">
    <li>
        <a
          href="{{ route('items.index', ['tab' => 'recommend', 'keyword' => request('keyword')]) }}"
          class="tab-link {{ ($tab ?? 'recommend') === 'recommend' ? 'active' : '' }}"
          data-tab="recommend"
        >
            おすすめ
        </a>
    </li>
    <li>
        <a
          href="{{ route('items.index', ['tab' => 'mylist',    'keyword' => request('keyword')]) }}"
          class="tab-link {{ ($tab ?? 'recommend') === 'mylist'    ? 'active' : '' }}"
          data-tab="mylist"
        >
            マイリスト
        </a>
    </li>
</ul>

    <div id="item-list-container">
        @include('items.partials.item_list', ['items' => $items, 'tab' => $tab ?? 'recommend'])
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/items/index.js') }}"></script>
@endsection
