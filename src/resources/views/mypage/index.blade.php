@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/mypage/index.css') }}">
@endsection

@section('content')
<div class="mypage-header">
    <div class="profile-image-container">
      <div class="profile-image-wrapper">
        @if($user->profile && $user->profile->profile_image)
          <img
            src="{{ asset($user->profile->profile_image) }}"
            alt="プロフィール画像"
            class="profile-image"
          >
        @else
          <div class="profile-avatar-placeholder"></div>
        @endif
      </div>
      <div class="profile-name">
        {{ $user->profile && $user->profile->name
            ? $user->profile->name
            : $user->name
        }}
      </div>

      <a href="{{ route('profile.edit') }}" class="profile-edit-btn">
        プロフィール編集
      </a>
    </div>
  </div>


<ul class="tab-menu">
  <li>
    <a href="#" class="tab-link {{ $tab === 'listed' ? 'active' : '' }}" data-tab="listed">
      出品した商品
    </a>
  </li>
  <li>
    <a href="#" class="tab-link {{ $tab === 'purchased' ? 'active' : '' }}" data-tab="purchased">
      購入した商品
    </a>
  </li>
</ul>

<div id="item-list-container">
  @include('mypage.partials.item_list', [
    'items' => $tab === 'listed' ? $listedItems : $purchasedItems,
    'tab'   => $tab
  ])
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/mypage/index.js') }}"></script>
@endsection
