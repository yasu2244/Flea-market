<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Middleware\VerifyCsrfToken;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF ミドルウェアを無効化
        $this->withoutMiddleware(VerifyCsrfToken::class);

        // 必要なシーダー
        $this->seed(\Database\Seeders\StatusesSeeder::class);
        $this->seed(\Database\Seeders\CategoriesSeeder::class);
    }

    /** @test */
    public function 「購入する」ボタンを押下すると購入が完了する()
    {
        $user = User::factory()->verifiedWithProfile()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->withSession([
                'purchase_address' => [
                    'postal_code' => '123-4567',
                    'address'     => '東京都千代田区1-1-1',
                    'building'    => 'ビル101',
                ],
            ])
            ->post("/purchase/{$item->id}", [
                'payment_method'   => 'card',
                // PurchaseRequestのhipping_address バリデーションを通すためのダミー
                'shipping_address' => 'テスト用ダミー',
            ]);

        // 正常にリダイレクトされること
        $response->assertRedirect();

        // DB にレコードが作成されていること
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function 購入した商品は商品一覧画面にて「sold」と表示される()
    {
        $user = User::factory()->verifiedWithProfile()->create();
        $item = Item::factory()->create();

        //  完了フラグを立てた購入レコードを作成
        Purchase::factory()
            ->for($user)
            ->for($item)
            ->create(['is_completed' => true]);

        // 商品側にも売切フラグを立てる
        $item->update(['is_sold' => true]);

        // recommend タブ（デフォルト）でも SOLD が出る
        $this->actingAs($user)
            ->get('/')
            ->assertStatus(200)
            ->assertSee('SOLD');
    }

    /** @test */
    public function プロフィールの購入した商品一覧に追加されている()
    {
        $user = User::factory()->verifiedWithProfile()->create();
        $item = Item::factory()->create();

        Purchase::factory()
            ->for($user)
            ->for($item)
            ->create(['is_completed' => true]);
        $item->update(['is_sold' => true]);

        $this->actingAs($user)
            ->get('/mypage?tab=buy')
            ->assertStatus(200)
            ->assertSee($item->name);
    }
}
