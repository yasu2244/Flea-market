<?php
namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatRoomController extends Controller
{
    public function show(ChatRoom $chatRoom)
    {
        // 未読を既読に
        ChatMessage::where('chat_room_id', $chatRoom->id)
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_by')
            ->update(['read_by' => Auth::id()]);

        // チャットルーム本体
        $room = $chatRoom->load(['messages.user.profile', 'item']);

        // サイドバー用
        $rooms = ChatRoom::where('buyer_id', Auth::id())
                         ->orWhere('seller_id', Auth::id())
                         ->with('item')
                         ->withCount(['messages as unread_messages_count' => function($q) {
                             $q->where('user_id', '!=', Auth::id())
                               ->whereNull('read_by');
                         }])
                         ->orderBy('updated_at', 'desc')
                         ->get();

        // 相手ユーザー
        $partner = Auth::id() === $room->buyer_id
                   ? $room->seller->load('profile')
                   : $room->buyer->load('profile');

        // チャットルームに対応する Purchase を取得
        $purchase = Purchase::where('item_id', $chatRoom->item_id)
                            ->where('user_id', $chatRoom->buyer_id)
                            ->firstOrFail();

        // ログインユーザーが「購入者か出品者か」を判定
        $isBuyer = (Auth::id() === $room->buyer_id);
        $isSeller = (Auth::id() === $room->seller_id);

        return view('chat.show', [
            'room'       => $room,
            'rooms'      => $rooms,
            'partner'    => $partner,
            'purchase'   => $purchase,
            'isBuyer'    => $isBuyer,
            'isSeller'   => $isSeller,
        ]);
    }

    public function complete(ChatRoom $chatRoom)
    {
        // モーダル用に取得
        $purchase = Purchase::where('item_id', $chatRoom->item_id)
                            ->where('user_id', $chatRoom->buyer_id)
                            ->firstOrFail();

        return view('chat.complete_modal', compact('chatRoom', 'purchase'));
    }
}
