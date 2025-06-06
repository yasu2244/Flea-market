<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Purchase;
use App\Notifications\TransactionCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function store(Request $request, Purchase $purchase)
    {
        // バリデーション
        $data = $request->validate([
            'rating'  => 'required|integer|between:1,5',
        ]);

        // 既に評価済みならリダイレクト
        if (Evaluation::where('purchase_id', $purchase->id)
                      ->where('rater_id', Auth::id())
                      ->exists()) {
            return redirect()
                ->route('items.index')
                ->with('status', '既に評価済みです');
        }

        // ratee_id（評価される側の user_id）を決定
        $rateeId = Auth::id() === $purchase->user_id
                   ? $purchase->item->user_id  // 購入者が評価 → ratee は出品者
                   : $purchase->user_id;       // 出品者が評価 → ratee は購入者

        Evaluation::create([
            'purchase_id' => $purchase->id,
            'rater_id'    => Auth::id(),
            'ratee_id'    => $rateeId,
            'rating'      => $data['rating'],
        ]);

        //  Purchase の buyer_rated または seller_rated を更新
        if (Auth::id() === $purchase->user_id) {
            // 購入者が評価した場合
            $purchase->update(['buyer_rated' => true]);

            // 出品者への通知メールを送る
            $seller = $purchase->item->user;
            $seller->notify(new TransactionCompleted($purchase));
        } else {
            // 出品者が評価した場合
            $purchase->update(['seller_rated' => true]);
        }

        return redirect()
            ->route('items.index')
            ->with('status', '評価を送信しました');
    }
}
