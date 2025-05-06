<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Category;
use App\Models\Status;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    /**
     * 商品一覧表示（タブ切替・検索対応）
     */
    public function index(Request $request)
    {
        $user    = Auth::user();
        $tab     = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword');

        if ($tab === 'mylist') {
            if (! $user) {
                // 未認証なら何も返さない
                $items = collect();
            } else {
                // 認証済みなら自分のいいねアイテムから「自分の出品」を除外
                $likedIds = DB::table('item_likes')
                    ->where('user_id', $user->id)
                    ->pluck('item_id');

                $items = Item::whereIn('id', $likedIds)
                    ->where('user_id', '<>', $user->id)
                    ->when($keyword, fn($q) => $q->where('name', 'like', "%{$keyword}%"))
                    ->latest()
                    ->get();
            }
        } else {
            // おすすめタブ（自分の出品は除外）
            $items = Item::when($user, fn($q) => $q->where('user_id', '<>', $user->id))
                ->when($keyword, fn($q) => $q->where('name', 'like', "%{$keyword}%"))
                ->latest()
                ->get();
        }

        return view('items.index', compact('items', 'tab', 'keyword'));
    }

    public function switchTab(Request $request)
    {
        $user    = Auth::user();
        $tab     = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword');

        if ($tab === 'mylist') {
            if ($user) {
                $likedIds = DB::table('item_likes')
                    ->where('user_id', $user->id)
                    ->pluck('item_id');

                $items = Item::whereIn('id', $likedIds)
                             ->where('user_id', '!=', $user->id)
                             ->when($keyword, fn($q) => $q->where('name', 'like', "%{$keyword}%"))
                             ->latest()
                             ->get();
            } else {
                $items = collect();
            }
        } else {
            $items = Item::when($user, fn($q) => $q->where('user_id', '!=', $user->id))
                         ->when($keyword, fn($q) => $q->where('name', 'like', "%{$keyword}%"))
                         ->latest()
                         ->get();
        }

        // 部分ビューを返す
        return view('items.partials.item_list', compact('items', 'tab', 'keyword'));
    }

    /**
     * 詳細表示
     */
    public function show($id)
    {
        $item = Item::with(['user', 'status', 'categories', 'comments.user'])
            ->findOrFail($id);
        return view('items.show', compact('item'));
    }

    /**
     * 出品フォーム表示
     */
    public function create()
    {
        $categories = Category::all();
        $statuses   = Status::all();
        return view('items.create', compact('categories', 'statuses'));
    }

    /**
     * 出品処理
     */
    public function store(ExhibitionRequest $request)
    {
        $data = $request->validated();
        $data['user_id']    = Auth::id();
        $data['image_path'] = $request->file('image')->store('items', 'public');

        $item = Item::create($data);
        $item->categories()->attach($data['categories']);

        return redirect()
            ->route('items.show', $item)
            ->with('success', '商品を出品しました');
    }
}
