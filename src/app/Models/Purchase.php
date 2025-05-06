<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    // 購入商品のリレーション
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // 購入した「ユーザー」へのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
