<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\PurchaseAddressRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    /**
     * 購入画面表示
     */
    public function show(Item $item)
    {
        // 既に購入済みの場合は商品一覧へリダイレクト
        if ($item->is_sold) {
            return redirect()
                ->route('items.index')
                ->with('status', 'この商品は既に売り切れです。');
        }

        $user = Auth::user();
        session()->put('purchase_item_id', $item->id);
        return view('purchase.show', compact('item', 'user'));
    }

    /**
     * 購入時住所編集フォーム表示
     */
    public function editAddress(Item $item)
    {
        $user = Auth::user();
        return view('purchase.address_edit', compact('user', 'item'));
    }

    /**
     * 購入時住所更新
     */
    public function updateAddress(PurchaseAddressRequest $request, Item $item)
    {
        $validated = $request->validated();
        session()->put('purchase_address', $validated);

        // リダイレクト先をモデル直接渡しに修正
        return redirect()->route('purchase.show', $item);
    }

    /**
     * 決済処理
     */
    public function store(PurchaseRequest $request, Item $item)
    {
         // 古い未完了レコードを削除（24時間以上前のもの）
        Purchase::where('is_completed', false)
            ->where('created_at', '<', now()->subHours(24))
            ->delete();

        // 商品購入済みか再チェック（安全のため）
        if ($item->is_sold) {
            return redirect()
                ->route('items.index')
                ->with('status', 'この商品は既に購入済みです。');
        }

        $user = Auth::user();

        $address = session('purchase_address') ?? [
            'postal_code' => $user->profile->postal_code,
            'address'     => $user->profile->address,
            'building'    => $user->profile->building,
        ];

        // 購入記録を作成
        $purchase = Purchase::create([
            'user_id'        => $user->id,
            'item_id'        => $item->id,
            'payment_method' => $request->input('payment_method'),
            'postal_code'    => $address['postal_code'],
            'address'        => $address['address'],
            'building'       => $address['building'],
            'price'          => $item->price,
            'is_completed'   => false,
        ]);

        return $this->createCheckoutSession($purchase);
    }

    /**
     * Stripe Checkout セッション作成
     */
    protected function createCheckoutSession(Purchase $purchase)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card', 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency'    => 'jpy',
                    'unit_amount' => $purchase->price,
                    'product_data'=> ['name' => $purchase->item->name],
                ],
                'quantity' => 1,
            ]],
            'mode'         => 'payment',
            'success_url'  => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'   => route('purchase.cancel'),
            'metadata'     => ['purchase_id' => $purchase->id],
        ]);

        $purchase->update(['stripe_session_id' => $session->id]);

        return redirect()->away($session->url);
    }

    /**
     * 決済結果ハンドリング
     */
    public function handleSuccess(Request $request)
    {
        $purchase = Purchase::where('stripe_session_id', $request->get('session_id'))->first();

        if ($purchase && !$purchase->is_completed) {
            // フラグ更新
            $purchase->update(['is_completed' => true]);
            $purchase->item->update(['is_sold' => true]);
        }

        return redirect()->route('items.index')->with('status', '購入が完了しました！');
    }
}
