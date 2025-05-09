<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Middleware\VerifyCsrfToken;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->seed(\Database\Seeders\StatusesSeeder::class);
    }

    /** @test */
    public function いいねアイコンを押下することによって、いいねした商品として登録することができる。()
    {
        // 認証＆プロフィール済みユーザー
        $user = User::factory()->verifiedWithProfile()->create();
        $item = Item::factory()->create();

        // いいねリクエスト
        $response = $this->actingAs($user)
            ->postJson("/item/{$item->id}/like");

        $response
            ->assertStatus(200)
            ->assertJson(['like_count' => 1]);

        // DB にレコードがあることを確認
        $this->assertDatabaseHas('item_likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function 追加済みのアイコンは色が変化する()
    {
        $user = User::factory()->verifiedWithProfile()->create();
        $item = Item::factory()->create();

        // いいねする
        $this->actingAs($user)->postJson("/item/{$item->id}/like");

        // いいねボタン押下でもう一度レスポンスをチェック
        $response = $this->actingAs($user)
            ->postJson("/item/{$item->id}/like");

        $response
            ->assertStatus(200)
            ->assertJson(['liked' => false]);
    }

    /** @test */
    public function 再度いいねアイコンを押下することによって、いいねを解除することができる。()
    {
        $user = User::factory()->verifiedWithProfile()->create();
        $item = Item::factory()->create();

        // いいね登録
        $this->actingAs($user)->postJson("/item/{$item->id}/like");
        $this->assertDatabaseHas('item_likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね解除リクエスト
        $response = $this->actingAs($user)
            ->deleteJson("/item/{$item->id}/like");

        $response
            ->assertStatus(200)
            ->assertJson(['like_count' => 0]);

        $this->assertDatabaseMissing('item_likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
