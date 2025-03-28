<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $items = Item::with('status')
            ->when($user, fn($q) => $q->where('user_id', '!=', $user->id))
            // ログイン済みユーザー：自分の商品を除外
            ->orderBy('created_at', 'desc')
            ->get();

        return view('items.index', compact('items'));
    }


    public function show($id)
    {
        $item = Item::with(['user', 'status', 'categories', 'comments.user'])->findOrFail($id);

        return view('items.show', compact('item'));
    }
}
