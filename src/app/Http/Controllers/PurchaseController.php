<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\PurchaseAddressRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

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

    public function store(PurchaseRequest $request, Item $item)
    {
        $user = Auth::user();

        $address = session('purchase_address') ?? [
            'postal_code' => $user->profile->postal_code,
            'address' => $user->profile->address,
            'building' => $user->profile->building,
        ];

        // 購入記録を作成
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

        return $this->createCheckoutSession($purchase);
    }

    protected function createCheckoutSession($purchase)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card', 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $purchase->price,
                    'product_data' => [
                        'name' => $purchase->item->name,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchase.cancel'),
            'metadata' => [
                'purchase_id' => $purchase->id,
            ],
        ]);

        $purchase->stripe_session_id = $session->id;
        $purchase->save();

        return redirect()->away($session->url);
    }

    public function handleSuccess(Request $request)
    {
        // Stripe セッションから purchase_id を取得
        $purchase = Purchase::where('stripe_session_id', $request->get('session_id'))->first();

        if ($purchase && !$purchase->is_completed) {
            // 購入フラグ更新
            $purchase->update(['is_completed' => true]);

            // 商品の販売済みフラグを更新
            $item = $purchase->item;
            $item->update(['is_sold' => true]);
        }

        return redirect()->route('items.index')->with('status', '購入が完了しました！');
    }
}
