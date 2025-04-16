<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseAddressRequest;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        $user = Auth::user();
        session()->put('purchase_item_id', $item->id);
        return view('purchase.show', compact('item', 'user'));
    }

    public function editAddress(Item $item)
    {
        $user = Auth::user();
        return view('purchase.address_edit', compact('user', 'item'));
    }

    public function updateAddress(PurchaseAddressRequest $request, Item $item)
    {
        $validated = $request->validated();
        session()->put('purchase_address', $validated);

        return redirect()->route('purchase.show', $item->id);
    }

    public function store(Request $request, Item $item)
    {
        $user = Auth::user();

        $address = session('purchase_address') ?? [
            'postal_code' => $user->profile->postal_code,
            'address' => $user->profile->address,
            'building' => $user->profile->building,
        ];

        // 購入記録を作成（未完了）
        $purchase = Purchase::create([
            'user_id'       => $user->id,
            'item_id'       => $item->id,
            'payment_method'=> $request->input('payment_method'),
            'postal_code'   => $address['postal_code'],
            'address'       => $address['address'],
            'building'      => $address['building'],
            'price'         => $item->price,
            'is_completed'  => false,
        ]);

        // Stripe Checkout セッション生成 → redirect
        return $this->startStripeCheckout($purchase);
    }

    public function handleSuccess(Request $request)
    {
        // Stripe セッションから purchase_id を取得（session_idで突合など）
        $purchase = Purchase::where('stripe_session_id', $request->get('session_id'))->first();

        if ($purchase && !$purchase->is_completed) {
            $purchase->update(['is_completed' => true]);

            // 商品の販売済みフラグ更新などもここで可能
        }

        return redirect()->route('purchase.thanks');
    }

}
