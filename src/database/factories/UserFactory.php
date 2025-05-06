<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Profile;

class UserFactory extends Factory
{
    protected static ?string $password;

    protected $model = User::class;

    public function definition(): array
    {
        return [
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
            // ’name’ は削除！users テーブルには存在しません
        ];
    }

    /**
     * プロフィールを一緒に作成する state
     */
    public function withProfile(array $overrides = []): static
    {
        return $this->afterCreating(function (User $user) use ($overrides) {
            Profile::factory()->create(array_merge([
                'user_id'     => $user->id,
                'name'        => $this->faker->name(),
                'postal_code' => '123-4567',
                'address'     => '東京都千代田区1-1-1',
                'building'    => 'ビル101',
                'profile_image' => null,
            ], $overrides));
        });
    }

    /**
     * メール認証／プロフィール完了済みにする既存メソッドも調整
     */
    public function verifiedWithProfile(array $profile = []): static
    {
        return $this->state(fn() => [
                'email_verified_at' => now(),
            ])
            ->withProfile($profile)
            ->state(fn() => [
                'profile_completed' => true,
            ]);
    }
}
