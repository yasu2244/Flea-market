<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\StatusesSeeder::class);
    }

    /** @test */
    public function 送付先住所変更画面で選択した住所が購入画面に反映される()
    {
        $user = User::factory()->verifiedWithProfile()->create();
        $item = Item::factory()->create();

        // 住所変更画面へ遷移し、新しい住所を登録
        $response = $this->actingAs($user)
                         ->post("/purchase/address/{$item->id}", [
                             'postal_code' => '123-4567',
                             'address'     => '東京都千代田区1-1-1',
                             'building'    => 'テストビル101',
                         ]);

        $response->assertRedirect("/purchase/{$item->id}");

        // 購入画面にアクセスして、新しい住所が表示されていること
        $response = $this->actingAs($user)->get("/purchase/{$item->id}");
        $response->assertSee('123-4567')
                 ->assertSee('東京都千代田区1-1-1')
                 ->assertSee('テストビル101');
    }

/** @test */
public function 購入時に登録した配送先が購入レコードに紐づいている()
{
    $user = User::factory()->verifiedWithProfile()->create();
    $item = Item::factory()->create();

    // 住所変更後に購入
    $this->actingAs($user)->post("/purchase/address/{$item->id}", [
        'postal_code' => '123-4567',
        'address'     => '東京都千代田区1-1-1',
        'building'    => 'テストビル101',
    ]);

    $this->actingAs($user)->post("/purchase/{$item->id}", [
        'payment_method'   => 'card',
        'shipping_address' => 'use_saved', // ダミー値
    ]);

    $this->assertDatabaseHas('purchases', [
        'user_id'     => $user->id,
        'item_id'     => $item->id,
        'postal_code' => '123-4567',
        'address'     => '東京都千代田区1-1-1',
        'building'    => 'テストビル101',
    ]);
}
}
