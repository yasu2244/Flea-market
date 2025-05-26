<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model
{
    protected $fillable = ['item_id', 'buyer_id', 'seller_id'];

    // 商品
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // 購入者
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // 出品者
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // メッセージ一覧
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}
