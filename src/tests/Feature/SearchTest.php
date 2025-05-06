<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品名で部分一致検索ができる()
    {
        $this->seed(\Database\Seeders\StatusesSeeder::class);
        $this->seed(\Database\Seeders\CategoriesSeeder::class);

        // 部分一致する商品とそうでない商品を作成
        $match   = Item::factory()->create(['name' => 'JavaScriptを楽しく学ぼう']);
        $noMatch = Item::factory()->create(['name' => 'Laravel 入門']);

        // 「JavaScript」で検索
        $response = $this->get('/?keyword=JavaScript');
        $response->assertStatus(200)
                 ->assertSee($match->name)
                 ->assertDontSee($noMatch->name);
    }

    /** @test */
    public function 検索状態がマイリストでも保持されている()
    {
        $this->seed(\Database\Seeders\StatusesSeeder::class);
        $this->seed(\Database\Seeders\CategoriesSeeder::class);

        // 認証済み＋プロフィール済みユーザーを用意
        $user = User::factory()->verifiedWithProfile()->create();

        // 検索ワードを付けてマイリストタブへアクセス
        $response = $this->actingAs($user)
            ->get('/?tab=mylist&keyword=テスト');

        $response->assertStatus(200)
            // 検索フォームの value 属性に「テスト」が残っていること
            ->assertSee('value=')
            ->assertSee('テスト');
    }
}
