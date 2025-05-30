@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/chat/show.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/chat/complete_modal.css') }}">
@endsection

@section('content')
<div class="chat-page">
    {{-- サイドバー：その他の取引 --}}
    <aside class="chat-sidebar">
        <h3>その他の取引</h3>
        <ul>
            @foreach($rooms as $otherRoom)
            <li>
                <a href="{{ route('chat_rooms.show', $otherRoom) }}">
                {{ $otherRoom->item->name }}
                </a>
            </li>
            @endforeach
        </ul>
    </aside>

    {{-- メインエリア --}}
    <section class="chat-main">
    {{-- ヘッダー：相手情報・完了ボタン --}}
    <div class="chat-header">
        <img src="{{ $partner->profile_image_url }}" alt="相手画像" class="avatar">
        <h2>
            「{{ optional($partner->profile)->name ?: $partner->name }}」さんとの取引画面
        </h2>
        {{-- 取引完了ボタン --}}
        <button
            type="button"
            id="openCompleteModal"
            class="btn-complete"
            data-evaluate-url="{{ route('purchase.evaluate', $purchase) }}"
        >
        取引を完了する
        </button>
    </div>

    {{-- 商品情報 --}}
    <div class="chat-product">
        <img src="{{ $room->item->image_url }}" alt="商品画像" class="product-thumb">
        <div class="product-info">
            <p class="product-name">{{ $room->item->name }}</p>
            <p class="product-price">¥{{ number_format($room->item->price) }}</p>
        </div>
    </div>

    {{-- チャット欄 --}}
    <div class="chat-messages">
        {{-- モーダル表示 --}}
        @include('chat.complete_modal')

        @foreach($room->messages as $msg)
            @php $isMine = $msg->user_id === auth()->id(); @endphp
            <div class="message-row {{ $isMine ? 'mine' : 'their' }}" data-msg-id="{{ $msg->id }}">
                {{-- プロフィール＋名前 --}}
                <div class="message-header">
                    <img src="{{ $msg->user->profile_image_url }}" alt="" class="avatar-small">
                    <span class="username">
                        {{ optional($msg->user->profile)->name ?: $msg->user->name }}
                    </span>
                </div>

                {{-- 通常の本文表示 --}}
                <div class="message-body js-body-{{ $msg->id }}">
                {!! nl2br(e($msg->body)) !!}
                @if($msg->image_path)
                    <img src="{{ asset('storage/'.$msg->image_path) }}" class="message-image">
                @endif
                </div>

                {{-- 編集用フォーム（初期は非表示） --}}
                <div class="js-edit-form js-form-{{ $msg->id }}" style="display:none"
                        data-update-url="{{ route('chat_messages.update', [$room, $msg]) }}">

                    <div class="error-message js-error-{{ $msg->id }}"></div>

                    {{-- .message-body と同じコンテナ --}}
                    <div class="message-body editing">
                        <textarea
                            name="body"
                            rows="2"
                            class="edit-textarea"
                        >{{ $msg->body }}
                        </textarea>
                    </div>

                    {{-- 保存／キャンセルも .message-actions を流用 --}}
                    <div class="message-actions">
                        <button type="button" class="btn-action js-save-btn">保存</button>
                        <button type="button" class="btn-action js-cancel">キャンセル</button>
                    </div>
                </div>

                {{-- 編集／削除ボタン --}}
                @if($isMine)
                <div class="message-actions js-actions-{{ $msg->id }}">
                    <button type="button" class="btn-action js-edit-btn">編集</button>
                    <form action="{{ route('chat_messages.destroy', [$room, $msg]) }}"
                            method="POST"
                            class="inline-form"
                            onsubmit="return confirm('本当にこのメッセージを削除してもよろしいですか？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-delete">削除</button>
                    </form>
                </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- プレビュー表示 --}}
    <div id="image-preview-container" class="image-preview-container"></div>

    {{-- メッセージ入力フォーム --}}
    <form action="{{ route('chat_messages.store', $room) }}"
            method="post"
            enctype="multipart/form-data"
            class="chat-input js-send-form"
            data-room-id="{{ $room->id }}"
        >

        @csrf

        @error('body')
            <div class="error-message">{{ $message }}</div>
        @enderror

        <div class="chat-input-row">
            <textarea name="body" rows="1" placeholder="メッセージを入力してください"></textarea>

            <label class="btn-upload">
                画像を追加
                <input type="file" name="image" accept=".png,.jpeg" id="chat-image-input" >
            </label>

            <button type="submit" class="btn-send">
                <img src="{{ asset('assets/images/send-icon.jpg') }}" alt="送信" class="send-icon">
            </button>
        </div>
    </form>

  </section>
</div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/chat/inline-edit.js') }}"></script>
    <script src="{{ asset('assets/js/chat/image-preview.js') }}"></script>
    <script src="{{ asset('assets/js/chat/chat-draft.js') }}"></script>
    <script src="{{ asset('assets/js/chat/complete_modal.js') }}"></script>
@endsection
