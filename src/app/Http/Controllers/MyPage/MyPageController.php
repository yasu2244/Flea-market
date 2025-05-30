<?php

namespace App\Http\Controllers\MyPage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\ChatRoom;

class MyPageController extends Controller
{
    public function index(Request $request)
    {
        $tab  = $request->query('tab', 'sell');
        $user = Auth::user();

        // ===== 全タブ共通：未読数付きでチャットルーム取得 =====
        $rooms = ChatRoom::where('buyer_id', $user->id)
                         ->orWhere('seller_id', $user->id)
                         ->with('item')
                         ->withCount(['messages as unread_messages_count' => function($q) use ($user) {
                              $q->where('user_id', '!=', $user->id)
                                ->whereNull('read_by');
                         }])
                         ->orderBy('updated_at', 'desc')
                         ->get();

        // ===== タブ別：表示アイテムを切り替え =====
        if ($tab === 'buy') {
            // 購入済み
            $items = Purchase::with('item')
                             ->where('user_id', $user->id)
                             ->where('is_completed', true)
                             ->get()
                             ->pluck('item');
        }
        elseif ($tab === 'chat') {
            // 取引中（チャット中）→ルームの item を表示
            $items = $rooms->pluck('item');
        }
        else {
            // 出品中
            $items = Item::where('user_id', $user->id)->get();
        }

        return view('mypage.index', compact('user','tab','rooms','items'));
    }

    public function switchTab(Request $request)
    {
        $tab  = $request->query('tab', 'sell');
        $user = Auth::user();

        if ($tab === 'chat') {
            $rooms = ChatRoom::where('buyer_id', $user->id)
                             ->orWhere('seller_id', $user->id)
                             ->with('item')
                             ->withCount(['messages as unread_messages_count' => function($q) use ($user) {
                                  $q->where('user_id', '!=', $user->id)
                                    ->whereNull('read_by');
                             }])
                             ->orderBy('updated_at', 'desc')
                             ->get();

            return view('mypage.partials.chat_room_list', compact('rooms'));
        }

        if ($tab === 'buy') {
            $items = Purchase::with('item')
                             ->where('user_id', $user->id)
                             ->where('is_completed', true)
                             ->get()
                             ->pluck('item');
        } else {
            $items = Item::where('user_id', $user->id)->get();
        }

        return view('mypage.partials.item_list', compact('items','tab'));
    }
}
