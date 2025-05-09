<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Status;
use App\Models\Category;
use App\Http\Middleware\VerifyCsrfToken;

class ItemCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    /** @test */
    public function 商品出品画面にて必要な情報が保存できること() // （カテゴリ、商品の状態、商品名、商品の説明、販売価格）
    {
        // マスターをシード
        $this->seed(\Database\Seeders\StatusesSeeder::class);
        $this->seed(\Database\Seeders\CategoriesSeeder::class);

        // プロフィール付きユーザーを作成＆ログイン
        $user = User::factory()->verifiedWithProfile()->create();

        // 出品ページを開く
        $response = $this->actingAs($user)->get('/sell');
        $response->assertStatus(200)
                 ->assertSee(Status::first()->name)
                 ->assertSee(Category::first()->name);

        // ストレージをモックし、テスト用ファイルを生成
        Storage::fake('public');
        $file = UploadedFile::fake()->image('product.jpg');

        // フォーム送信
        $postData = [
            'name'         => 'テスト商品',
            'brand'        => 'テストブランド',
            'description'  => '商品の詳細説明です。',
            'price'        => 5000,
            'status_id'    => Status::first()->id,
            'categories'   => [Category::first()->id],
            'image'        => $file,
        ];
        $response = $this->actingAs($user)->post('/sell', $postData);

        // POST 後はリダイレクト
        $response->assertStatus(302);

        // テーブルにデータが登録されていることを確認
        $this->assertDatabaseHas('items', [
            'name'        => 'テスト商品',
            'brand'       => 'テストブランド',
            'description' => '商品の詳細説明です。',
            'price'       => 5000,
            'status_id'   => Status::first()->id,
            'user_id'     => $user->id,
        ]);

        // image_path を取得して null でないことを確認
        $imagePath = DB::table('items')
                       ->where('name', 'テスト商品')
                       ->value('image_path');
        $this->assertNotNull($imagePath);

        // ストレージにファイルが存在することを検証
        Storage::disk('public')->assertExists($imagePath);
    }
}
