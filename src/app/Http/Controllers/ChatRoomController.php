<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatRoomController extends Controller
{
    public function show(ChatRoom $chatRoom)
    {
        // ① この画面を開いたら、自分以外の未読メッセージを既読に
        ChatMessage::where('chat_room_id', $chatRoom->id)
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_by')
            ->update([
                'read_by' => Auth::id(),
            ]);

        // ② メインのチャットルームをロード（メッセージ・ユーザー・プロフィール・商品）
        $room = $chatRoom->load([
            'messages.user.profile',
            'item',
        ]);

        // ③ サイドバー用：自分が関与する全チャットルームを未読カウント付きで取得
        $rooms = ChatRoom::where('buyer_id', Auth::id())
                         ->orWhere('seller_id', Auth::id())
                         ->with('item')
                         ->withCount([
                             'messages as unread_messages_count' => function ($q) {
                                 $q->where('user_id', '!=', Auth::id())
                                   ->whereNull('read_by');
                             }
                         ])
                         ->orderBy('updated_at', 'desc')
                         ->get();

        // ④ 相手ユーザーを取得
        if (Auth::id() === $room->buyer_id) {
            $partner = $room->seller->load('profile');
        } else {
            $partner = $room->buyer->load('profile');
        }

        // ⑤ ビューへ渡す
        return view('chat.show', compact('room', 'rooms', 'partner'));
    }
}
