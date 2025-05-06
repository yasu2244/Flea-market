<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginFactory extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい情報でログインできる()
    {
        // テスト用ユーザーをファクトリーで作成
        $user = User::factory()->create([
            'email'    => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        // ログインリクエスト
        $response = $this->post('/login', [
            'email'    => 'user@example.com',
            'password' => 'password123',
        ]);

        // 検証
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}
