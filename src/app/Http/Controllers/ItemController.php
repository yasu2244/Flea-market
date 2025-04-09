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

        $query = Item::with('status')
            ->when($user, fn($q) => $q->where('user_id', '!=', $user->id))
            ->when($keyword, function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });

        $items = $query->orderBy('created_at', 'desc')->get();

        // いいね済み商品のID一覧
        $likedItemIds = $user
            ? \DB::table('item_likes')->where('user_id', $user->id)->pluck('item_id')->toArray()
            : [];

        // 検索結果がすべてマイリスト商品の場合
        $onlyLikedItems = $items->every(fn($item) => in_array($item->id, $likedItemIds));

        if ($onlyLikedItems && count($items) > 0) {
            // マイリスト用のアイテム取得
            $mylistItems = Item::whereIn('id', $likedItemIds)
                ->when($keyword, fn($q) => $q->where('name', 'like', '%' . $keyword . '%'))
                ->latest()
                ->get();

            return view('items.index', [
                'items' => $mylistItems,
                'tab' => 'mylist',
            ]);
        }

        return view('items.index', [
            'items' => $items,
            'tab' => 'recommend', // 現在のタブの状態
        ]);
    }

    public function switchTab(Request $request)
    {
        $tab = $request->query('tab');

        if ($tab === 'mylist') {
            if (!Auth::check()) {
                return view('items.partials.item_list', [
                    'items' => collect([]),
                    'tab' => 'mylist',
                ]);
            }

            $items = Item::whereIn('id', function ($query) {
                $query->select('item_id')
                      ->from('item_likes')
                      ->where('user_id', Auth::id());
            })->latest()->get();

            return view('items.partials.item_list', [
                'items' => $items,
                'tab' => 'mylist',
            ]);
        }

        // おすすめ
        $query = Item::query();

        // ログインしている場合は、自分の商品を除外
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

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
