<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        $user = Auth::user();
        return view('purchase.show', compact('item', 'user'));
    }
}
