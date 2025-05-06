<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Status;
use App\Models\Category;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報が全て表示される()
    {
        $this->seed(\Database\Seeders\StatusesSeeder::class);
        $this->seed(\Database\Seeders\CategoriesSeeder::class);

        // DB からマスターを取得
        $status     = Status::where('name', '良好')->firstOrFail();
        // 先頭3件を使う
        $categories = Category::all()->take(3);

        // アイテムを作成し、status_id と categories を紐付け
        $item = Item::factory()->create([
            'name'        => 'テスト商品',
            'brand'       => 'テストブランド',
            'price'       => 9876,
            'description' => '詳細な説明テキストです。',
            'image_path'  => 'images/test.jpg',
            'status_id'   => $status->id,
        ]);
        $item->categories()->sync($categories->pluck('id'));

        // 「いいね」用ユーザーを用意
        $likeUser = User::factory()->create();
        $item->likes()->attach($likeUser->id);

        // コメント用ユーザーを作成
        $commentUser = User::factory()->create();
        // プロフィールを別途作成
        Profile::factory()->create([
            'user_id'       => $commentUser->id,
            'name'          => 'テスター',
            'postal_code'   => '123-4567',
            'address'       => '東京都千代田区1-1-1',
            'building'      => 'ビル101',
            'profile_image' => 'profiles/avatar.jpg',
        ]);

        // コメントを作成
        Comment::factory()
            ->for($item)
            ->for($commentUser)
            ->create(['content' => '素晴らしい商品です']);

        // 商品詳細ページへアクセスして検証
        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200)
                 // 画像・名前・ブランド・価格・説明
                 ->assertSee($item->image_path)
                 ->assertSee($item->name)
                 ->assertSee($item->brand)
                 ->assertSee(number_format($item->price))
                 ->assertSee($item->description)

                 // いいね数・コメント数
                 ->assertSee((string) $item->likes_count)
                 ->assertSee((string) $item->comments_count)

                 // カテゴリ名（3つとも）
                 ->assertSee($categories[0]->name)
                 ->assertSee($categories[1]->name)
                 ->assertSee($categories[2]->name)

                 // 状態名
                 ->assertSee($status->name)

                 // コメントユーザーのプロフィール画像・名前・コメント内容
                 ->assertSee('profiles/avatar.jpg')
                 ->assertSee('テスター')
                 ->assertSee('素晴らしい商品です');
    }

    /** @test */
    public function 複数選択されたカテゴリが表示されているか()
    {
        $this->seed(\Database\Seeders\StatusesSeeder::class);
        $this->seed(\Database\Seeders\CategoriesSeeder::class);

        // 先頭3件カテゴリを取得
        $categories = Category::all()->take(3);

        // アイテムを作成して紐付け
        $item = Item::factory()->create();
        $item->categories()->sync($categories->pluck('id'));

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
