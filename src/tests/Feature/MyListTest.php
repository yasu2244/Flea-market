<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\StatusesSeeder::class);
        $this->seed(\Database\Seeders\CategoriesSeeder::class);
    }

    /** @test */
    public function いいねした商品だけが表示される()
    {
        $user     = User::factory()->verifiedWithProfile()->create();
        $liked    = Item::factory()->create(['name' => 'LIKED_ITEM']);
        $notLiked = Item::factory()->create(['name' => 'NOT_LIKED_ITEM']);

        $user->likedItems()->attach($liked->id);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200)
                ->assertSee('LIKED_ITEM')
                ->assertDontSee('NOT_LIKED_ITEM');
    }

    /** @test */
    public function マイリストの購入済み商品に_sold_ラベルが表示される()
    {
        $user = User::factory()->verifiedWithProfile()->create();
        $item = Item::factory()->create();

        // いいね
        $user->likedItems()->attach($item->id);

        // 購入レコードを作成＆フラグを立てる
        Purchase::factory()->for($user)->for($item)->create(['is_completed' => true]);
        $item->update(['is_sold' => true]);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200)
                 ->assertSee('SOLD');
    }

    /** @test */
    public function 自分が出品した商品はマイリストに表示されない()
    {
        $user    = User::factory()->verifiedWithProfile()->create();
        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'name'    => 'OWN_ITEM',
        ]);

        $otherItem = Item::factory()->create(['name' => 'OTHER_ITEM']);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200)
                ->assertDontSee('OWN_ITEM')
                ->assertDontSee('OTHER_ITEM'); // マイリストタブでは未いいねの他商品も出ないので
    }

    /** @test */
    public function 未認証の場合は何も表示されない()
    {
        $user    = User::factory()->verifiedWithProfile()->create();
        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'name'    => 'OWN_ITEM',
        ]);

        $otherItem = Item::factory()->create(['name' => 'OTHER_ITEM']);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200)
                 ->assertDontSee('OWN_ITEM')
                 ->assertDontSee('OTHER_ITEM');
    }
}
