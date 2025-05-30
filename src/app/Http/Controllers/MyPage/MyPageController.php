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

        // 「取引中の商品」タブで表示するルームのフィルタ条件
        $baseChatQuery = ChatRoom::where(function($q) use ($user) {
                $q->where('buyer_id', $user->id)
                ->orWhere('seller_id', $user->id);
            })
            // 支払い済みの purchase
            ->whereHas('purchase', function($q) {
                $q->where('is_completed', true);
            })
            // 自分がまだ評価していない purchase
            ->whereDoesntHave('purchase.evaluations', function($q) use ($user) {
                $q->where('rater_id', $user->id);
            });

        // 初回ロード時のバッジ件数
        $chatRoomCount = $baseChatQuery->count();

        // タブごとに実際に取り出すアイテム or ルーム
        $rooms = collect();
        $items = collect();

        if ($tab === 'chat') {
            $rooms = $baseChatQuery
                ->with(['item'])
                ->withCount(['messages as unread_messages_count' => function($q) use ($user) {
                    $q->where('user_id', '!=', $user->id);
                }])
                ->orderBy('updated_at', 'desc')
                ->get();
            $items = $rooms->pluck('item');
        }
        elseif ($tab === 'buy') {
            $items = Purchase::with('item')
                ->where('user_id', $user->id)
                ->where('is_completed', true)
                ->get()
                ->pluck('item');
        }
        else { // sell
            $items = Item::where('user_id', $user->id)->get();
        }

        return view('mypage.index', compact(
            'user', 'tab', 'rooms', 'items', 'chatRoomCount'
        ));
    }

    public function switchTab(Request $request)
    {
        $tab    = $request->query('tab', 'sell');
        $userId = Auth::id();

        if ($tab === 'chat') {
            $rooms = ChatRoom::where(function($q) use ($userId) {
                                $q->where('buyer_id',  $userId)
                                ->orWhere('seller_id', $userId);
                            })
                            // 支払い済み（Purchase.is_completed = true）だけを対象…
                            ->whereHas('purchase', function($q) {
                                $q->where('is_completed', true);
                            })
                            // 自分が評価済みの purchase は除外
                            ->whereDoesntHave('purchase.evaluations', function($q) use ($userId) {
                                $q->where('rater_id', $userId);
                            })
                            ->with(['item'])
                            ->withCount(['messages as unread_messages_count' => function($q) use ($userId) {
                                $q->where('user_id', '!=', $userId);
                            }])
                            ->orderBy('updated_at', 'desc')
                            ->get();

            // JSON 返却
            return response()->json([
                'html'      => view('mypage.partials.chat_room_list', compact('rooms'))->render(),
                'roomCount' => $rooms->count(),
            ]);
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

        $html = view('mypage.partials.item_list', compact('items', 'tab'))->render();

        return response()->json([
            'html'      => $html,
            'roomCount' => null,
        ]);
    }
}
