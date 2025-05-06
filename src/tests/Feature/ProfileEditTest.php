<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 変更項目が初期値として過去設定されていること() // （プロフィール画像、ユーザー名、郵便番号、住所）
    {
        // 既存のプロフィール情報を持つユーザーを作成
        $user = User::factory()->verifiedWithProfile([
            'name'          => 'テスト太郎',
            'postal_code'   => '123-4567',
            'address'       => '東京都千代田区1-1-1',
            'building'      => 'ビル101',
            'profile_image' => 'avatars/test.jpg',
        ])->create();

        // マイページ → プロフィール編集画面を開く
        $response = $this->actingAs($user)->get('/mypage/profile');
        $response->assertStatus(200);

        // プロフィール画像のプレビュー
        $response->assertSee('avatars/test.jpg');

        // 入力フォームの“値”だけを確認
        $response->assertSee('テスト太郎');
        $response->assertSee('123-4567');
        $response->assertSee('東京都千代田区1-1-1');
        $response->assertSee('ビル101');
    }
}
