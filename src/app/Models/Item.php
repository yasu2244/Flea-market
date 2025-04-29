<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'description',
        'price',
        'image_path',
        'status_id',
        'is_sold',
    ];

    // price を整数、is_sold を真偽値としてキャスト
    protected $casts = [
        'price'   => 'integer',
        'is_sold' => 'boolean',
    ];

    // 画像パスからフル URL を返すアクセサ
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? asset("storage/{$this->image_path}")
            : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'item_likes')->withTimestamps();
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
