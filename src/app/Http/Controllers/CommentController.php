<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Product;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Product $item)
    {
        Comment::create([
            'user_id' => auth()->id(),
            'product_id' => $item->id,
            'content' => $request->content,
        ]);

        return redirect()->route('items.show', $item->id)->with('success', 'コメントを投稿しました');
    }
}
