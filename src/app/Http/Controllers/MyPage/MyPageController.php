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
        $tab   = $request->query('tab', 'sell');
        $user  = Auth::user();

        // tab に関わらず必ず定義しておく
        $rooms = collect();
        $items = collect();

        if ($tab === 'chat') {
            $rooms = ChatRoom::where('buyer_id', $user->id)
                        ->orWhere('seller_id', $user->id)
                        ->withCount('messages as unread_messages_count')
                        ->orderBy('updated_at', 'desc')
                        ->get();

            // チャットタブ用にアイテム一覧をpluck
            $items = $rooms->pluck('item');
        }
        elseif ($tab === 'buy') {
            $items = Purchase::with('item')
                        ->where('user_id', $user->id)
                        ->where('is_completed', true)
                        ->get()
                        ->pluck('item');
        }
        else {
            $items = Item::where('user_id', $user->id)->get();
        }

        return view('mypage.index', compact('user', 'tab', 'rooms', 'items'));
    }

    public function switchTab(Request $request)
    {
        $tab    = $request->query('tab', 'sell');
        $userId = Auth::id();

        if ($tab === 'chat') {
            $rooms = ChatRoom::where('buyer_id', $userId)
                        ->orWhere('seller_id', $userId)
                        ->withCount('messages as unread_messages_count')
                        ->orderBy('updated_at', 'desc')
                        ->get();

            return view('mypage.partials.chat_room_list', compact('rooms'));
        }

        if ($tab === 'buy') {
            $items = Purchase::with('item')
                        ->where('user_id', $userId)
                        ->where('is_completed', true)
                        ->get()
                        ->pluck('item');
        } else {
            $items = Item::where('user_id', $userId)->get();
        }

        return view('mypage.partials.item_list', compact('items', 'tab'));
    }
}
