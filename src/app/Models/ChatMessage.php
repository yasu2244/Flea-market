<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = ['chat_room_id', 'user_id', 'body', 'image_path'];

    // どのチャットルームのメッセージか
    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }

    // 送信ユーザー
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
