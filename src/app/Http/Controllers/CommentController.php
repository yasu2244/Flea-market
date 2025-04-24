<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'コメントを投稿するにはログインが必要です');
        }

        Comment::create([
            'user_id' => auth()->id(),
            'item_id' => $item->id,
            'content' => $request->content,
        ]);

        return redirect()->route('items.show', $item->id)->with('success', 'コメントを投稿しました');
    }
}
