<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品を取得できる()
    {
        $this->seed(\Database\Seeders\StatusesSeeder::class);

        // ファクトリーで3件の商品を作成
        $items = Item::factory()->count(3)->create();

        // 商品一覧ページにアクセス
        $response = $this->get('/');
        $response->assertStatus(200);

        // 作成した全商品の名前が表示されていること
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    /** @test */
    public function 購入済み商品には_sold_ラベルが表示される()
    {
        $this->seed(\Database\Seeders\StatusesSeeder::class);

        // 購入ユーザーと商品を用意
        $buyer = User::factory()->create();
        $item  = Item::factory()->create();

        // 購入レコードと完了フラグ
        Purchase::factory()
            ->for($buyer)
            ->for($item)
            ->create(['is_completed' => true]);

        // 商品にも売切フラグを立てる
        $item->update(['is_sold' => true]);

        // 商品一覧ページにアクセス
        $response = $this->get('/');
        $response->assertStatus(200);
        // Sold ラベルが表示される
        $response->assertSee('SOLD');
    }

    /** @test */
    public function 自分が出品した商品は一覧に表示されない()
    {
        $this->seed(\Database\Seeders\StatusesSeeder::class);
        $statusId = \App\Models\Status::first()->id;

        // 出品ユーザー（認証済み＋プロフィール済み）
        $user = User::factory()->verifiedWithProfile()->create();

        // 自分が出品した商品を「OWN_ITEM」という固定名で作成
        $ownItem = Item::factory()->create([
            'user_id'   => $user->id,
            'status_id' => $statusId,
            'name'      => 'OWN_ITEM',
        ]);

        // 他ユーザーの商品を「OTHER_ITEM_1」「OTHER_ITEM_2」で作成
        $otherItems = Item::factory()->count(2)->create([
            'status_id' => $statusId,
            'name'      => function () {
                static $i = 1;
                return 'OTHER_ITEM_' . $i++;
            },
        ]);

        // ログインし、一覧ページにアクセス
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200)
            // OWN_ITEM が表示されない
            ->assertDontSee('OWN_ITEM');

        // 自分の商品名が含まれないこと
        $response->assertDontSee($ownItem->name);

        // 他ユーザーの商品は必ず見える
        $response->assertSee('OTHER_ITEM_1')
                ->assertSee('OTHER_ITEM_2');
    }
}
