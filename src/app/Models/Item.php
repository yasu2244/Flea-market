<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    protected $casts = [
        'price'   => 'integer',
        'is_sold' => 'boolean',
    ];

    /**
     * 画像パスからフル URL を返すアクセサ
     */
    public function getImageUrlAttribute(): ?string
    {
        $path = $this->image_path;
        if (! $path) {
            return null;
        }
        return Str::startsWith($path, 'assets/')
            ? asset($path)
            : asset('storage/' . $path);
    }

    /**
     * 出品者（ユーザー）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 商品状態
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * カテゴリー（多対多）
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * コメント
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * いいねしたユーザー
     */
    public function likes()
    {
        return $this->belongsToMany(User::class, 'item_likes')->withTimestamps();
    }

    /**
     * 指定ユーザーがいいね済みか判定
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * 購入履歴
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
