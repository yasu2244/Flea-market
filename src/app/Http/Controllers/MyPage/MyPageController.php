<?php

namespace App\Http\Controllers\MyPage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Purchase;

class MyPageController extends Controller
{
    public function index(Request $request)
    {
        // タブ指定（'listed' or 'purchased'）
        $tab = $request->query('tab', 'listed');

        // 現在ユーザーと profile リレーションをロード
        $user = Auth::user()->load('profile');

        // 出品商品
        $listedItems = Item::where('user_id', $user->id)->get();

        // 購入済み商品
        $purchasedItems = Purchase::with('item')
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->get()
            ->pluck('item');

        return view('mypage.index', [
            'user'           => $user,
            'tab'            => $tab,
            'listedItems'    => $listedItems,
            'purchasedItems' => $purchasedItems,
        ]);
    }

    public function switchTab(Request $request)
    {
        $tab = $request->query('tab', 'listed');
        $userId = Auth::id();

        if ($tab === 'listed') {
            $items = Item::where('user_id', $userId)->get();
        } else {
            $items = Purchase::with('item')
                ->where('user_id', $userId)
                ->where('is_completed', true)
                ->get()
                ->pluck('item');
        }

        return view('mypage.partials.item_list', compact('items', 'tab'));
    }
}
