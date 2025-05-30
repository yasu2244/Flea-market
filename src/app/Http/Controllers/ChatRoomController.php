<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatRoomController extends Controller
{
    public function show(ChatRoom $chatRoom)
    {
        // メインルーム（メッセージ／ユーザー／プロフィール／商品をまとめて取得）
        $room = $chatRoom->load([
            'messages.user.profile',
            'item'
        ]);

        // サイドバー用ルーム一覧
        $rooms = ChatRoom::where('buyer_id', Auth::id())
                        ->orWhere('seller_id', Auth::id())
                        ->with('item')
                        ->orderBy('updated_at', 'desc')
                        ->get();

        // 相手ユーザー
        $partner = Auth::id() === $room->buyer_id
                ? $room->seller->load('profile')
                : $room->buyer->load('profile');

        return view('chat.show', compact('room', 'rooms', 'partner'));
    }
}
