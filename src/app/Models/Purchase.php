<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'postal_code',
        'address',
        'building',
        'price',
        'is_completed',
    ];

    // 購入商品のリレーション
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
