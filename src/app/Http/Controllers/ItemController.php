<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Models\Status;
use App\Http\Requests\ExhibitionRequest;

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

    // 出品フォーム
    public function create()
    {
        return view('items.create', [
            'categories' => Category::all(),
            'statuses'   => Status::all(),
        ]);
    }

    // 出品処理
    public function store(ExhibitionRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['image_path'] = $request->file('image')
                                    ->store('items', 'public');
        $item = Item::create($data);
        $item->categories()->attach($data['categories']);

        return redirect()
            ->route('items.show', $item)
            ->with('success', '商品を出品しました');
    }
}
