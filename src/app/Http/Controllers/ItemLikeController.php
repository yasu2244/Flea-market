<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemLikeController extends Controller
{
    public function toggle(Item $item, Request $request)
    {
        $user = $request->user();

        if ($item->isLikedBy($user)) {
            $item->likes()->detach($user->id);
            $liked = false;
        } else {
            $item->likes()->attach($user->id);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'like_count' => $item->likes()->count(),
        ]);
    }
}
