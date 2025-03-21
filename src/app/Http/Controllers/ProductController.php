<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 自分が出品した商品を除外し、購入済みかどうかを判定して取得
        $products = Product::with(['user', 'status'])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('products.index', compact('products'));
    }
}
