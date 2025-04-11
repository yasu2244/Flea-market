<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $keyword = $request->keyword;

        // 自分の商品を除く「おすすめ商品」
        $recommendItems = Item::with('status')
            ->when($user, fn($q) => $q->where('user_id', '!=', $user->id))
            ->when($keyword, fn($q) => $q->where('name', 'like', '%' . $keyword . '%'))
            ->latest()
            ->get();

        // いいね済み商品の ID 一覧
        $likedItemIds = $user
            ? \DB::table('item_likes')->where('user_id', $user->id)->pluck('item_id')->toArray()
            : [];

        // マイリスト商品に対しても検索
        $mylistItems = Item::whereIn('id', $likedItemIds)
            ->when($keyword, fn($q) => $q->where('name', 'like', '%' . $keyword . '%'))
            ->latest()
            ->get();

        // 検索結果がマイリストにしか存在しない場合
        if ($recommendItems->isEmpty() && $mylistItems->isNotEmpty()) {
            return view('items.index', [
                'items' => $mylistItems,
                'tab' => 'mylist',
                'keyword' => $keyword,
            ]);
        }

        // 検索結果がマイリストにもおすすめにもない場合
        if ($recommendItems->isEmpty() && $mylistItems->isEmpty()) {
            return view('items.index', [
                'items' => collect([]),
                'tab' => 'recommend',
                'keyword' => $keyword,
            ]);
        }

        // 通常（おすすめ商品がある）
        return view('items.index', [
            'items' => $recommendItems,
            'tab' => 'recommend',
            'keyword' => $keyword,
        ]);
    }


    public function switchTab(Request $request)
    {
        $tab = $request->query('tab');
        $keyword = $request->query('keyword');
        $user = Auth::user();

        if ($tab === 'mylist') {
            if (!$user) {
                return view('items.partials.item_list', [
                    'items' => collect([]),
                    'tab' => 'mylist',
                ]);
            }

            $likedItemIds = \DB::table('item_likes')
                ->where('user_id', $user->id)
                ->pluck('item_id');

            $items = Item::whereIn('id', $likedItemIds)
                ->when($keyword, fn($q) => $q->where('name', 'like', "%{$keyword}%"))
                ->latest()->get();

            return view('items.partials.item_list', [
                'items' => $items,
                'tab' => 'mylist',
            ]);
        }

        // おすすめ
        $query = Item::query()
            ->when($user, fn($q) => $q->where('user_id', '!=', $user->id))
            ->when($keyword, fn($q) => $q->where('name', 'like', "%{$keyword}%"));

        $items = $query->latest()->get();

        return view('items.partials.item_list', [
            'items' => $items,
            'tab' => 'recommend',
        ]);
    }

    public function show($id)
    {
        $item = Item::with(['user', 'status', 'categories', 'comments.user'])->findOrFail($id);

        return view('items.show', compact('item'));
    }
}
