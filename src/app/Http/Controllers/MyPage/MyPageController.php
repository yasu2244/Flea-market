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

        // チャットタブの共通クエリ（購入者／出品者いずれかを対象にする）
        $baseChatQuery = ChatRoom::where(function($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
            })
            // まず「決済済み(true) の Purchase」だけ残す
            ->whereHas('purchase', function($q) {
                $q->where('is_completed', true);
            })
            // 購入者視点なら buyer_rated=false の Purchase
            // 出品者視点なら seller_rated=false の Purchase
            ->whereHas('purchase', function($q) use ($user) {
                $q->where(function($q2) use ($user) {
                    // 自分が購入者の場合
                    $q2->where('user_id', $user->id)
                       ->where('buyer_rated', false);
                })
                ->orWhere(function($q2) use ($user) {
                    // 自分が出品者の場合
                    $q2->whereColumn('item_id', 'purchases.item_id')
                       ->where('purchases.user_id', '!=', $user->id) // 出品者は purchase.user_id ではないので
                       ->where('seller_rated', false);
                });
            });

        // 初回ロード時のバッジ件数
        $chatRoomCount = $baseChatQuery->count();

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
                            // 決済済み Purchase のみ
                            ->whereHas('purchase', function($q) {
                                $q->where('is_completed', true);
                            })
                            // 購入者なら buyer_rated=false、出品者なら seller_rated=false をチェック
                            ->whereHas('purchase', function($q) use ($userId) {
                                $q->where(function($q2) use ($userId) {
                                    $q2->where('user_id', $userId)
                                       ->where('buyer_rated', false);
                                })
                                ->orWhere(function($q2) use ($userId) {
                                    $q2->whereColumn('item_id', 'purchases.item_id')
                                       ->where('purchases.user_id', '!=', $userId)
                                       ->where('seller_rated', false);
                                });
                            })
                            ->with(['item'])
                            ->withCount(['messages as unread_messages_count' => function($q) use ($userId) {
                                $q->where('user_id', '!=', $userId);
                            }])
                            ->orderBy('updated_at', 'desc')
                            ->get();

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
