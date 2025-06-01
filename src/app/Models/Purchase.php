<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'postal_code',
        'address',
        'building',
        'price',
        'is_completed',
        'stripe_session_id',
        'buyer_rated',
        'seller_rated',
    ];

    // 購入商品
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // 購入者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // この購入に対する評価一覧
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
