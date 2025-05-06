<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\StatusesSeeder::class);
    }

    /** @test */
    public function ログイン済みユーザーはコメントを送信できる()
    {
        $user = User::factory()->verifiedWithProfile()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post("/item/{$item->id}/comment", [
                'content' => '素晴らしい商品です',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => '素晴らしい商品です',
        ]);
    }

    /** @test */
    public function ログイン前のユーザーはコメントを送信できない()
    {
        $item = Item::factory()->create();

        $response = $this->post("/item/{$item->id}/comment", [
            'content' => 'テストコメント',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function コメントが入力されていない場合、バリデーションメッセージが表示される()
    {
        $user = User::factory()->verifiedWithProfile()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post("/item/{$item->id}/comment", [
                'content' => '',
            ]);

        $response->assertSessionHasErrors([
            'content' => 'コメントを入力してください',
        ]);
    }

    /** @test */
    public function コメントが255字以上の場合、バリデーションメッセージが表示される()
    {
        $user = User::factory()->verifiedWithProfile()->create();
        $item = Item::factory()->create();

        $long = str_repeat('あ', 256);

        $response = $this->actingAs($user)
            ->post("/item/{$item->id}/comment", [
                'content' => $long,
            ]);

        $response->assertSessionHasErrors([
            'content' => 'コメントは255文字以内で入力してください',
        ]);
    }
}
