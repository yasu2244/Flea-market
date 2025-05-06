<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\StatusesSeeder::class);
        $this->seed(\Database\Seeders\CategoriesSeeder::class);
    }

    /** @test */
    public function 「購入する」ボタンを押下すると購入が完了する()
    {

        $this->seed(\Database\Seeders\StatusesSeeder::class);

        $user = User::factory()->verifiedWithProfile()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post("/purchase/{$item->id}", [
                'payment_method'   => 'card',
                'shipping_address' => '自宅',
                'postal_code'      => '123-4567',
                'address'          => '東京都千代田区1-1-1',
                'building'         => 'ビル101',
            ]);

        $response->assertRedirect();

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

        // ① 完了フラグを立てた購入レコードを作成
        Purchase::factory()
            ->for($user)
            ->for($item)
            ->create(['is_completed' => true]);

        // ② 商品側にも売切フラグを立てる
        $item->update(['is_sold' => true]);

        // のちに recommend タブ（デフォルト）でも SOLD が出る
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
