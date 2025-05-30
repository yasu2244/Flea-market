<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'profile_completed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'profile_completed' => 'boolean',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'item_likes')->withTimestamps();
    }

        public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }

    public function evaluationsGiven()
    {
        return $this->hasMany(Evaluation::class, 'rater_id');
    }

    public function evaluationsReceived()
    {
        return $this->hasMany(Evaluation::class, 'ratee_id');
    }

    public function receivedEvaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'ratee_id');
    }

    // 平均評価を丸めて返すアクセサ
    public function getAverageRatingAttribute(): int
    {
        // 評価がなければ 0
        $avg = $this->receivedEvaluations()->avg('rating') ?? 0;

        // 四捨五入して整数化
        return (int) round($avg);
    }

    public function getProfileImageUrlAttribute(): string
    {
        $path = optional($this->profile)->profile_image;
        if (! $path) {
            return asset('assets/default-avatar.png');
        }
        return Str::startsWith($path, 'assets/')
            ? asset($path)
            : asset('storage/'.$path);
    }

}
