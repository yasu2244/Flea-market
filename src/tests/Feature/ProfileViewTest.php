<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Status;

class ProfileViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報が取得できる() //（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
    {
        // まずはステータスのマスター投入
        $this->seed(\Database\Seeders\StatusesSeeder::class);

        // テスト用ユーザーとプロフィール
        $user = User::factory()->verifiedWithProfile()->create();
        $status = Status::first();

        // 出品アイテムを明示的な名前で２つ作成
        $sell1 = Item::factory()->create([
            'user_id'   => $user->id,
            'status_id' => $status->id,
            'name'      => '出品商品A',
        ]);
        $sell2 = Item::factory()->create([
            'user_id'   => $user->id,
            'status_id' => $status->id,
            'name'      => '出品商品B',
        ]);

        // 他ユーザーのアイテムを購入扱いにする
        $other = User::factory()->verifiedWithProfile()->create();
        $buyItem = Item::factory()->create([
            'user_id'   => $other->id,
            'status_id' => $status->id,
            'name'      => '購入商品X',
        ]);
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $buyItem->id,
        ]);

        // --- 出品タブ (/mypage?tab=sell) の検証 ---
        $response = $this->actingAs($user)->get('/mypage?tab=sell');
        $response->assertStatus(200)
                 // プロフィール画像と名前は両タブ共通
                 ->assertSee('profile-image')
                 ->assertSee($user->profile->name)
                 // 自分の出品商品は表示される
                 ->assertSee('出品商品A')
                 ->assertSee('出品商品B')
                 // 購入商品は表示されない
                 ->assertDontSee('購入商品X');

        // --- 購入タブ (/mypage?tab=buy) の検証 ---
        $response = $this->actingAs($user)->get('/mypage?tab=buy');
        $response->assertStatus(200)
                 ->assertSee('profile-image')
                 ->assertSee($user->profile->name)
                 // 購入商品だけ表示される
                 ->assertSee('購入商品X')
                 ->assertDontSee('出品商品A')
                 ->assertDontSee('出品商品B');
    }
}
