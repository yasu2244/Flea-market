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
        // タブ指定
        $tab = $request->query('tab', 'sell');
        $user = Auth::user();

        if ($tab === 'buy') {
            // 購入済み商品
            $items = Purchase::with('item')
                        ->where('user_id', $user->id)
                        ->where('is_completed', true)
                        ->get()
                        ->pluck('item');
        } else {
            // 出品商品
            $items = Item::where('user_id', $user->id)->get();
        }

        return view('mypage.index', compact('items', 'tab', 'user'));
    }

    public function switchTab(Request $request)
    {
        $tab    = $request->query('tab', 'sell');
        $userId = Auth::id();

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
